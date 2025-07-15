import { useState } from 'react'
import { CheckCircleIcon, XCircleIcon } from '@heroicons/react/24/outline'
import { ErrorModal } from './ErrorModal'

export function RecordsTable({ records }) {
  const [modalOpen, setModalOpen] = useState(false)
  const [selectedRecord, setSelectedRecord] = useState(null)

  const openModal = (record) => {
    setSelectedRecord(record)
    setModalOpen(true)
  }
  const closeModal = () => {
    setModalOpen(false)
    setSelectedRecord(null)
  }

  const validRecords = records.filter(record => record.is_valid)
  const invalidRecords = records.filter(record => !record.is_valid)

  return (
    <div>
      <div className="mb-4">
        <h2 className="text-xl font-semibold text-gray-900 mb-2">Validation Results</h2>
        <div className="flex space-x-4 text-sm">
          <span className="text-green-600">
            {validRecords.length} valid records
          </span>
          <span className="text-red-600">
            {invalidRecords.length} invalid records
          </span>
        </div>
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Row</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {records.map((record) => (
              <tr key={record.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{record.row_number}</td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center">
                    {record.is_valid ? (
                      <CheckCircleIcon className="h-5 w-5 text-green-500 mr-2" />
                    ) : (
                      <XCircleIcon className="h-5 w-5 text-red-500 mr-2" />
                    )}
                    <span className={`text-sm font-medium ${record.is_valid ? 'text-green-600' : 'text-red-600'}`}>{record.status_badge}</span>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{record.name || '-'}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{record.email || '-'}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{record.phone || '-'}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{record.gender || '-'}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {!record.is_valid && (
                    <button
                      onClick={() => openModal(record)}
                      className="text-blue-600 hover:text-blue-900"
                    >
                      View Errors
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <ErrorModal isOpen={modalOpen} onClose={closeModal} record={selectedRecord} />
    </div>
  )
} 