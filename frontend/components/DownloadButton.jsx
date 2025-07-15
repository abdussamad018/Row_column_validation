import { useState } from 'react'
import { ArrowDownTrayIcon } from '@heroicons/react/24/outline'
import { downloadErrorReport, downloadFile } from '../lib/api.js'

export function DownloadButton({ importId }) {
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState(null)

  const handleDownload = async () => {
    setIsLoading(true)
    setError(null)
    try {
      const response = await downloadErrorReport(importId)
      if (response.success && response.data) {
        downloadFile(importId)
      } else {
        setError(response.message || 'Failed to generate report')
      }
    } catch (err) {
      setError((err && err.response && err.response.data && err.response.data.message) || 'Download failed')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div>
      <div className="mb-4">
        <h2 className="text-xl font-semibold text-gray-900 mb-2">Download Error Report</h2>
        <p className="text-sm text-gray-600">
          Download an Excel file containing all invalid records with their error messages.
        </p>
      </div>
      <button
        onClick={handleDownload}
        disabled={isLoading}
        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {isLoading ? (
          <>
            <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            Generating Report...
          </>
        ) : (
          <>
            <ArrowDownTrayIcon className="h-4 w-4 mr-2" />
            Download Error Report
          </>
        )}
      </button>
      {error && (
        <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p className="text-red-700 text-sm">{error}</p>
        </div>
      )}
    </div>
  )
} 