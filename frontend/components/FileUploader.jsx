import { useCallback, useState } from 'react'
import { useDropzone } from 'react-dropzone'
import { CloudArrowUpIcon, DocumentArrowUpIcon } from '@heroicons/react/24/outline'
import { uploadFile } from '../lib/api.js'

export function FileUploader({ onImportComplete, setIsLoading }) {
  const [error, setError] = useState(null)

  const onDrop = useCallback(async (acceptedFiles) => {
    if (acceptedFiles.length === 0) return
    const file = acceptedFiles[0]
    setError(null)
    setIsLoading(true)
    try {
      const response = await uploadFile(file)
      if (response.success && response.data) {
        onImportComplete(response.data)
      } else {
        setError(response.message || 'Upload failed')
      }
    } catch (err) {
      setError((err && err.response && err.response.data && err.response.data.message) || 'Upload failed')
    } finally {
      setIsLoading(false)
    }
  }, [onImportComplete, setIsLoading])

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept: {
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': ['.xlsx'],
      'application/vnd.ms-excel': ['.xls'],
    },
    maxFiles: 1,
    maxSize: 10 * 1024 * 1024, // 10MB
  })

  return (
    <div>
      <div className="mb-4">
        <h2 className="text-xl font-semibold text-gray-900 mb-2">Upload Excel File</h2>
        <p className="text-sm text-gray-600">
          Drag and drop an Excel file (.xlsx, .xls) or click to browse. Maximum file size: 10MB
        </p>
      </div>
      <div
        {...getRootProps()}
        className={`border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors ${isDragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'}`}
      >
        <input {...getInputProps()} />
        {isDragActive ? (
          <div>
            <CloudArrowUpIcon className="mx-auto h-12 w-12 text-blue-500 mb-4" />
            <p className="text-blue-600 font-medium">Drop the file here...</p>
          </div>
        ) : (
          <div>
            <DocumentArrowUpIcon className="mx-auto h-12 w-12 text-gray-400 mb-4" />
            <p className="text-gray-600 mb-2">
              <span className="font-medium text-blue-600">Click to upload</span> or drag and drop
            </p>
            <p className="text-xs text-gray-500">Excel files only (.xlsx, .xls)</p>
          </div>
        )}
      </div>
      {error && (
        <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p className="text-red-700 text-sm">{error}</p>
        </div>
      )}
      <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 className="font-medium text-blue-900 mb-2">Expected Excel Format:</h3>
        <div className="text-sm text-blue-800 space-y-1">
          <p>• <strong>Name</strong> (required): String, max 255 characters</p>
          <p>• <strong>Email</strong> (required): Valid email format</p>
          <p>• <strong>Phone</strong> (optional): Phone number format</p>
          <p>• <strong>Gender</strong> (required): "M" or "F"</p>
        </div>
      </div>
    </div>
  )
} 