'use client'

import { useState } from 'react'
import { FileUploader } from '../components/FileUploader'
import { ImportStatus } from '../components/ImportStatus'
import { RecordsTable } from '../components/RecordsTable'
import { DownloadButton } from '../components/DownloadButton'

export default function Home() {
  const [currentImport, setCurrentImport] = useState(null)
  const [records, setRecords] = useState([])
  const [isLoading, setIsLoading] = useState(false)

  const handleImportComplete = (importData) => {
    setCurrentImport(importData)
  }

  const handleRecordsLoaded = (recordsData) => {
    setRecords(recordsData)
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Excel Importer
          </h1>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            Upload Excel files and validate data with real-time feedback. 
            Get detailed error reports and download failed records.
          </p>
        </div>
        {/* File Upload Section */}
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <FileUploader 
            onImportComplete={handleImportComplete}
            setIsLoading={setIsLoading}
          />
        </div>
        {/* Import Status */}
        {currentImport && (
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <ImportStatus 
              importData={currentImport}
              onRecordsLoaded={handleRecordsLoaded}
            />
          </div>
        )}
        {/* Records Table */}
        {records.length > 0 && (
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <RecordsTable records={records} />
          </div>
        )}
        {/* Download Section */}
        {currentImport && currentImport.status === 'completed' && currentImport.invalid_rows > 0 && (
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <DownloadButton importId={currentImport.id} />
          </div>
        )}
        {/* Loading Overlay */}
        {isLoading && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg p-6 flex items-center space-x-3">
              <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
              <span className="text-gray-700">Processing your file...</span>
            </div>
          </div>
        )}
      </div>
    </div>
  )
} 