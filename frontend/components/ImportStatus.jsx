import { useEffect, useState } from 'react'
import { CheckCircleIcon, XCircleIcon, ClockIcon } from '@heroicons/react/24/outline'
import { getImportStatus, getImportRecords } from '../lib/api.js'

export function ImportStatus({ importData, onRecordsLoaded }) {
  const [currentData, setCurrentData] = useState(importData)
  const [records, setRecords] = useState([])

  useEffect(() => {
    setCurrentData(importData)
  }, [importData])

  useEffect(() => {
    if (currentData.status === 'completed' || currentData.status === 'failed') {
      loadRecords()
    } else if (currentData.status === 'processing') {
      const interval = setInterval(pollStatus, 2000)
      return () => clearInterval(interval)
    }
  }, [currentData.status, currentData.id])

  const pollStatus = async () => {
    try {
      const response = await getImportStatus(currentData.id)
      if (response.success && response.data) {
        setCurrentData(response.data)
      }
    } catch (error) {
      console.error('Error polling status:', error)
    }
  }

  const loadRecords = async () => {
    try {
      const response = await getImportRecords(currentData.id)
      if (response.success && response.data) {
        setRecords(response.data)
        onRecordsLoaded(response.data)
      }
    } catch (error) {
      console.error('Error loading records:', error)
    }
  }

  const getStatusIcon = () => {
    switch (currentData.status) {
      case 'completed':
        return <CheckCircleIcon className="h-6 w-6 text-green-500" />
      case 'failed':
        return <XCircleIcon className="h-6 w-6 text-red-500" />
      default:
        return <ClockIcon className="h-6 w-6 text-blue-500" />
    }
  }

  const getStatusColor = () => {
    switch (currentData.status) {
      case 'completed':
        return 'text-green-600'
      case 'failed':
        return 'text-red-600'
      default:
        return 'text-blue-600'
    }
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-xl font-semibold text-gray-900">Import Status</h2>
          <p className="text-sm text-gray-600">File: {currentData.filename}</p>
        </div>
        <div className="flex items-center space-x-2">
          {getStatusIcon()}
          <span className={`font-medium ${getStatusColor()}`}>{currentData.status_text}</span>
        </div>
      </div>
      {currentData.status === 'processing' && (
        <div className="mb-6">
          <div className="flex justify-between text-sm text-gray-600 mb-2">
            <span>Processing...</span>
            <span>{currentData.progress_percentage}%</span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div
              className="bg-blue-600 h-2 rounded-full transition-all duration-300"
              style={{ width: `${currentData.progress_percentage}%` }}
            ></div>
          </div>
        </div>
      )}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div className="bg-gray-50 p-4 rounded-lg">
          <div className="text-2xl font-bold text-gray-900">{currentData.total_rows}</div>
          <div className="text-sm text-gray-600">Total Rows</div>
        </div>
        <div className="bg-gray-50 p-4 rounded-lg">
          <div className="text-2xl font-bold text-gray-900">{currentData.processed_rows}</div>
          <div className="text-sm text-gray-600">Processed</div>
        </div>
        <div className="bg-green-50 p-4 rounded-lg">
          <div className="text-2xl font-bold text-green-600">{currentData.valid_rows}</div>
          <div className="text-sm text-green-600">Valid Records</div>
        </div>
        <div className="bg-red-50 p-4 rounded-lg">
          <div className="text-2xl font-bold text-red-600">{currentData.invalid_rows}</div>
          <div className="text-sm text-red-600">Invalid Records</div>
        </div>
      </div>
      {currentData.error_message && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-lg mb-6">
          <p className="text-red-700 text-sm">{currentData.error_message}</p>
        </div>
      )}
      <div className="text-sm text-gray-500 space-y-1">
        <p>Started: {new Date(currentData.started_at).toLocaleString()}</p>
        {currentData.completed_at && (
          <p>Completed: {new Date(currentData.completed_at).toLocaleString()}</p>
        )}
      </div>
    </div>
  )
} 