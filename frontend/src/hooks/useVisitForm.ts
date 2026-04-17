import { useState, useCallback, useEffect } from 'react'
import { Visit, FormErrors } from '@/types'
import visitService from '@/services/api'

interface UseVisitFormReturn {
  visit: Partial<Visit>
  errors: FormErrors
  isLoading: boolean
  setVisitField: (field: keyof Visit, value: any) => void
  setVisitFields: (fields: Partial<Visit>) => void
  setError: (field: string, message: string) => void
  clearError: (field: string) => void
  saveVisit: () => Promise<{ id_visita: string }> // Primera vez
  updateVisit: (id: string, stepData?: Record<string, any>) => Promise<void> // Actualizaciones
  loadVisit: (id: string) => Promise<void>
  resetForm: () => void
}

const INITIAL_VISIT: Partial<Visit> = {
  country: '',
  format: '',
  store: '',
  visit_email: '',
  bv_pais: '',
  id_sugar_tienda: '',
}

export function useVisitForm(): UseVisitFormReturn {
  const [visit, setVisit] = useState<Partial<Visit>>(INITIAL_VISIT)
  const [errors, setErrors] = useState<FormErrors>({})
  const [isLoading, setIsLoading] = useState(false)

  const setVisitField = useCallback((field: keyof Visit, value: any) => {
    setVisit((prev) => ({
      ...prev,
      [field]: value,
    }))
    // Limpiar error del campo cuando se actualiza
    if (errors[field]) {
      setErrors((prev) => {
        const newErrors = { ...prev }
        delete newErrors[field]
        return newErrors
      })
    }
  }, [errors])

  const setVisitFields = useCallback((fields: Partial<Visit>) => {
    setVisit((prev) => ({
      ...prev,
      ...fields,
    }))
  }, [])

  const setError = useCallback((field: string, message: string) => {
    setErrors((prev) => ({
      ...prev,
      [field]: message,
    }))
  }, [])

  const clearError = useCallback((field: string) => {
    setErrors((prev) => {
      const newErrors = { ...prev }
      delete newErrors[field]
      return newErrors
    })
  }, [])

  const saveVisit = useCallback(async (): Promise<{ id_visita: string }> => {
    setIsLoading(true)
    try {
      const result = await visitService.createVisit(visit)
      return { id_visita: result.id_visita }
    } catch (error: any) {
      const errorMessage = error.response?.data?.error || 'Error al guardar la visita'
      setError('form', errorMessage)
      throw error
    } finally {
      setIsLoading(false)
    }
  }, [visit, setError])

  const updateVisit = useCallback(async (id: string, stepData?: Record<string, any>): Promise<void> => {
    setIsLoading(true)
    try {
      const dataToSend = stepData ? { ...visit, ...stepData } : visit
      await visitService.updateVisit(id, dataToSend)
    } catch (error: any) {
      const errorMessage = error.response?.data?.error || 'Error al actualizar la visita'
      setError('form', errorMessage)
      throw error
    } finally {
      setIsLoading(false)
    }
  }, [visit, setError])

  const loadVisit = useCallback(async (id: string): Promise<void> => {
    setIsLoading(true)
    try {
      const data = await visitService.getVisit(id)
      setVisit(data)
    } catch (error: any) {
      const errorMessage = error.response?.data?.error || 'Error al cargar la visita'
      setError('form', errorMessage)
      throw error
    } finally {
      setIsLoading(false)
    }
  }, [setError])

  const resetForm = useCallback(() => {
    setVisit(INITIAL_VISIT)
    setErrors({})
  }, [])

  return {
    visit,
    errors,
    isLoading,
    setVisitField,
    setVisitFields,
    setError,
    clearError,
    saveVisit,
    updateVisit,
    loadVisit,
    resetForm,
  }
}
