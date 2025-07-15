<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Models\Import;
use App\Models\ImportRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        // Add debugging
        \Log::info('Upload request received', [
            'has_file' => $request->hasFile('file'),
            'all_data' => $request->all(),
        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . $originalFilename;
            
            \Log::info('File details', [
                'original_name' => $originalFilename,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
            
            // Store the file
            $filePath = $file->storeAs('imports', $filename, 'local');
            
            \Log::info('File stored', ['path' => $filePath]);

            // Create import record
            $import = Import::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'status' => 'pending',
                'started_at' => now(),
            ]);
            
            \Log::info('Import record created', ['import_id' => $import->id]);

            // Process the file in background (for now, we'll process immediately)
            $this->processImport($import, $filePath);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'import_id' => $import->id,
                'data' => [
                    'id' => $import->id,
                    'filename' => $originalFilename,
                    'status' => $import->status,
                    'total_rows' => $import->total_rows,
                    'processed_rows' => $import->processed_rows,
                    'valid_rows' => $import->valid_rows,
                    'invalid_rows' => $import->invalid_rows,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status($id): JsonResponse
    {
        $import = Import::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $import->id,
                'filename' => $import->original_filename,
                'status' => $import->status,
                'status_text' => $import->status_text,
                'total_rows' => $import->total_rows,
                'processed_rows' => $import->processed_rows,
                'valid_rows' => $import->valid_rows,
                'invalid_rows' => $import->invalid_rows,
                'progress_percentage' => $import->progress_percentage,
                'started_at' => $import->started_at,
                'completed_at' => $import->completed_at,
                'error_message' => $import->error_message,
            ]
        ]);
    }

    public function records($id): JsonResponse
    {
        $import = Import::findOrFail($id);
        $records = ImportRecord::where('import_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'row_number' => $record->row_number,
                    'name' => $record->name,
                    'email' => $record->email,
                    'phone' => $record->phone,
                    'age' => $record->age,
                    'department' => $record->department,
                    'is_valid' => $record->is_valid,
                    'errors' => $record->errors,
                    'errors_text' => $record->errors_text,
                    'status_badge' => $record->status_badge,
                    'processed_at' => $record->processed_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    public function downloadErrors($id): JsonResponse
    {
        $import = Import::findOrFail($id);
        
        if ($import->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Import is not completed yet'
            ], 400);
        }

        try {
            $invalidRecords = ImportRecord::where('import_id', $id)
                ->where('is_valid', false)
                ->orderBy('row_number')
                ->get();

            if ($invalidRecords->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invalid records to download'
                ], 404);
            }

            // Create Excel file with errors
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Add headers
            $sheet->setCellValue('A1', 'Row Number');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'Email');
            $sheet->setCellValue('D1', 'Phone');
            $sheet->setCellValue('E1', 'Age');
            $sheet->setCellValue('F1', 'Department');
            $sheet->setCellValue('G1', 'Errors');

            $row = 2;
            foreach ($invalidRecords as $record) {
                $sheet->setCellValue('A' . $row, $record->row_number);
                $sheet->setCellValue('B' . $row, $record->name);
                $sheet->setCellValue('C' . $row, $record->email);
                $sheet->setCellValue('D' . $row, $record->phone);
                $sheet->setCellValue('E' . $row, $record->age);
                $sheet->setCellValue('F' . $row, $record->department);
                $sheet->setCellValue('G' . $row, $record->errors_text);
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Save file
            $errorFilename = 'error_report_' . $import->id . '_' . time() . '.xlsx';
            $errorFilePath = 'imports/' . $errorFilename;
            
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/' . $errorFilePath));

            // Update import with error file path
            $import->update(['error_file_path' => $errorFilePath]);

            return response()->json([
                'success' => true,
                'message' => 'Error report generated successfully',
                'data' => [
                    'download_url' => url('/api/imports/' . $id . '/download-file'),
                    'filename' => $errorFilename,
                    'invalid_count' => $invalidRecords->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile($id)
    {
        $import = Import::findOrFail($id);
        
        if (!$import->error_file_path || !Storage::exists($import->error_file_path)) {
            abort(404, 'Error report not found');
        }

        return Storage::download($import->error_file_path);
    }

    protected function processImport(Import $import, string $filePath): void
    {
        try {
            \Log::info('Starting import process', ['import_id' => $import->id, 'file_path' => $filePath]);
            
            $import->update(['status' => 'processing']);

            // Get total rows count
            $rows = Excel::toArray(new EmployeeImport($import), $filePath);
            $totalRows = count($rows[0]) - 1; // Subtract header row
            
            \Log::info('Excel file read', ['total_rows' => $totalRows]);
            
            $import->update(['total_rows' => $totalRows]);

            // Process the import
            $importClass = new EmployeeImport($import);
            Excel::import($importClass, $filePath);

            // Update final counts
            $import->update([
                'status' => 'completed',
                'valid_rows' => $importClass->getValidCount(),
                'invalid_rows' => $importClass->getInvalidCount(),
                'completed_at' => now(),
            ]);
            
            \Log::info('Import completed', [
                'valid_count' => $importClass->getValidCount(),
                'invalid_count' => $importClass->getInvalidCount(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Import processing error', [
                'import_id' => $import->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
} 