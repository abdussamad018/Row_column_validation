import { Fragment } from 'react'
import { Dialog, Transition } from '@headlessui/react'
import { XMarkIcon } from '@heroicons/react/24/outline'

export function ErrorModal({ isOpen, onClose, record }) {
  if (!record) return null

  return (
    <Transition.Root show={isOpen} as={Fragment}>
      <Dialog as="div" className="relative z-50" onClose={onClose}>
        <Transition.Child
          as={Fragment}
          enter="ease-out duration-300"
          enterFrom="opacity-0"
          enterTo="opacity-100"
          leave="ease-in duration-200"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
        >
          <div className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </Transition.Child>

        <div className="fixed inset-0 z-10 overflow-y-auto">
          <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <Transition.Child
              as={Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enterTo="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100 translate-y-0 sm:scale-100"
              leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <Dialog.Panel className="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div className="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                  <button
                    type="button"
                    className="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onClick={onClose}
                  >
                    <span className="sr-only">Close</span>
                    <XMarkIcon className="h-6 w-6" aria-hidden="true" />
                  </button>
                </div>
                <div className="sm:flex sm:items-start">
                  <div className="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <XMarkIcon className="h-6 w-6 text-red-600" aria-hidden="true" />
                  </div>
                  <div className="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                    <Dialog.Title as="h3" className="text-base font-semibold leading-6 text-gray-900">
                      Validation Errors - Row {record.row_number}
                    </Dialog.Title>
                    <div className="mt-4">
                      <div className="bg-gray-50 p-4 rounded-lg mb-4">
                        <h4 className="text-sm font-medium text-gray-900 mb-2">Record Data:</h4>
                        <div className="grid grid-cols-2 gap-2 text-sm text-gray-600">
                          <div><span className="font-medium">Name:</span> {record.name || '-'}</div>
                          <div><span className="font-medium">Email:</span> {record.email || '-'}</div>
                          <div><span className="font-medium">Phone:</span> {record.phone || '-'}</div>
                          <div><span className="font-medium">Gender:</span> {record.gender || '-'}</div>
                        </div>
                      </div>
                      
                      <div className="space-y-3">
                        <h4 className="text-sm font-medium text-gray-900">Error Details:</h4>
                        {record.errors && Object.entries(record.errors).map(([field, errors]) => (
                          <div key={field} className="border-l-4 border-red-400 pl-3">
                            <span className="text-sm font-medium text-red-800 capitalize">{field}:</span>
                            <ul className="mt-1 space-y-1">
                              {Array.isArray(errors) ? errors.map((error, index) => (
                                <li key={index} className="text-sm text-red-700 flex items-start">
                                  <span className="text-red-500 mr-1">•</span>
                                  {error}
                                </li>
                              )) : (
                                <li className="text-sm text-red-700 flex items-start">
                                  <span className="text-red-500 mr-1">•</span>
                                  {errors}
                                </li>
                              )}
                            </ul>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                  <button
                    type="button"
                    className="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                    onClick={onClose}
                  >
                    Close
                  </button>
                </div>
              </Dialog.Panel>
            </Transition.Child>
          </div>
        </div>
      </Dialog>
    </Transition.Root>
  )
} 