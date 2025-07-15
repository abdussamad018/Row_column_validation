import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'multipart/form-data',
  },
})

export const uploadFile = async (file) => {
  const formData = new FormData()
  formData.append('file', file)
  
  const response = await api.post('/imports/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
  
  return response.data
}

export const getImportStatus = async (id) => {
  const response = await api.get(`/imports/${id}`)
  return response.data
}

export const getImportRecords = async (id) => {
  const response = await api.get(`/imports/${id}/records`)
  return response.data
}

export const downloadErrorReport = async (id) => {
  const response = await api.post(`/imports/${id}/download-errors`)
  return response.data
}

export const downloadFile = (id) => {
  window.open(`/api/imports/${id}/download-file`, '_blank')
} 