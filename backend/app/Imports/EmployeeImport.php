<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Import;
use App\Models\ImportRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeeImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected Import $import;
    protected array $errors = [];
    protected int $validCount = 0;
    protected int $invalidCount = 0;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of heading row and 0-based index
            
            // Validate the row
            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'gender' => 'required|string|in:M,F',
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name cannot exceed 255 characters',
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.max' => 'Email cannot exceed 255 characters',
                'phone.max' => 'Phone number cannot exceed 20 characters',
                'gender.required' => 'Gender is required',
                'gender.in' => 'Gender must be M or F',
            ]);

            $isValid = !$validator->fails();
            $errors = $validator->errors()->toArray();

            // Create import record
            ImportRecord::create([
                'import_id' => $this->import->id,
                'row_number' => $rowNumber,
                'name' => $row['name'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'gender' => $row['gender'] ?? null,
                'is_valid' => $isValid,
                'errors' => $errors,
                'processed_at' => now(),
            ]);

            // If valid, create employee record
            if ($isValid) {
                try {
                    Employee::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'phone' => $row['phone'] ?? null,
                        'gender' => $row['gender'],
                        'import_id' => $this->import->id,
                    ]);
                    $this->validCount++;
                } catch (\Exception $e) {
                    // Handle duplicate email or other database errors
                    $errors['database'] = ['Database error: ' . $e->getMessage()];
                    ImportRecord::where('import_id', $this->import->id)
                        ->where('row_number', $rowNumber)
                        ->update([
                            'is_valid' => false,
                            'errors' => $errors,
                        ]);
                    $this->invalidCount++;
                }
            } else {
                $this->invalidCount++;
            }

            // Update import progress
            $this->import->increment('processed_rows');
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getValidCount(): int
    {
        return $this->validCount;
    }

    public function getInvalidCount(): int
    {
        return $this->invalidCount;
    }
} 