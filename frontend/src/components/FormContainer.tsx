import React, { useState, useEffect } from 'react'
import { useVisitForm } from '@/hooks/useVisitForm'
import { Step1 } from '@/components/Step1'
import { StepForm } from '@/components/StepForm'
import { Step9 } from '@/components/Step9'
import { ProgressBar } from '@/components/ProgressBar'
import { Header } from '@/components/Header'
import { AlertCircle, CheckCircle2 } from 'lucide-react'

const TOTAL_STEPS = 9

// Mapeo: currentStep 2-8 corresponde a secciones 1-7, currentStep 9 es resumen/envío

export function FormContainer() {
  const [currentStep, setCurrentStep] = useState(1)
  const [visitId, setVisitId] = useState<string | null>(null)
  const [successMessage, setSuccessMessage] = useState('')
  const [errorMessage, setErrorMessage] = useState('')
  const [stepData, setStepData] = useState<Record<number, Record<string, any>>>({})
  const [isSubmittingForm, setIsSubmittingForm] = useState(false)

  const {
    visit,
    errors,
    isLoading,
    setVisitField,
    setVisitFields,
    setError,
    clearError,
    saveVisit,
    updateVisit,
  } = useVisitForm()

  // Validar email
  const isValidEmail = (email: string) => {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/
    return emailRegex.test(email)
  }

  const handleStep1Next = async () => {
    // Validaciones
    if (!visit.country) {
      setError('country', 'Selecciona un país')
      return
    }
    if (!visit.format) {
      setError('format', 'Selecciona un formato')
      return
    }
    if (!visit.store) {
      setError('store', 'Selecciona una tienda')
      return
    }
    if (!visit.visit_email) {
      setError('visit_email', 'Ingresa un correo electrónico')
      return
    }
    if (!isValidEmail(visit.visit_email)) {
      setError('visit_email', 'Correo electrónico no válido')
      return
    }

    try {
      setErrorMessage('')

      // Si es la primera vez, crear la visita
      if (!visitId) {
        const storeData = JSON.parse(visit.store as string)
        const countryAbbreviations: Record<string, string> = {
          'El Salvador': 'SV',
          'Guatemala': 'GT',
          'Nicaragua': 'NI',
          'Honduras': 'HN',
          'Costa Rica': 'CR',
          'Panama': 'PA',
        }

        const visitData = {
          country: visit.country,
          format: visit.format,
          store: storeData.nombre,
          visit_email: visit.visit_email,
          bv_pais: countryAbbreviations[visit.country as string] || visit.country,
          id_sugar_tienda: storeData.pais_tienda,
          store_email: storeData.email,
          ubicacion: storeData.ubicacion,
        }

        const result = await saveVisit()
        setVisitId(result.id_visita)
        setSuccessMessage('Información inicial guardada correctamente')
      }

      // Avanzar al siguiente paso
      setCurrentStep(currentStep + 1)
    } catch (error: any) {
      setErrorMessage(error.response?.data?.error || 'Error al guardar los datos')
    }
  }

  const handlePreviousStep = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1)
      setSuccessMessage('')
    }
  }

  const handleNextStep = async () => {
    try {
      setErrorMessage('')
      setSuccessMessage('')

      // Guardar datos del paso actual
      if (visitId && currentStep > 1) {
        // Actualizar con datos del paso actual
        if (stepData[currentStep]) {
          await updateVisit(visitId, stepData[currentStep])
        }
        setSuccessMessage(`Paso ${currentStep} guardado correctamente`)
      }

      if (currentStep < TOTAL_STEPS) {
        setCurrentStep(currentStep + 1)
      }
    } catch (error: any) {
      setErrorMessage(error.response?.data?.error || 'Error al guardar los datos')
    }
  }

  const handleFormSubmit = async () => {
    try {
      setIsSubmittingForm(true)
      setErrorMessage('')

      // Guardar datos finales del paso 9
      if (visitId) {
        await updateVisit(visitId, stepData[9])
        setSuccessMessage('¡Formulario completado y enviado exitosamente!')
      }

      // Aquí podrías redirigir a una página de éxito después de unos segundos
      setTimeout(() => {
        // window.location.href = '/success'
      }, 2000)
    } catch (error: any) {
      setErrorMessage(error.response?.data?.error || 'Error al guardar los datos')
    } finally {
      setIsSubmittingForm(false)
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-brand-dark/5 to-brand-yellow/5">
      <Header currentStep={currentStep} totalSteps={TOTAL_STEPS} />

      <div className="container mx-auto px-4 py-8">
        {/* Barra de progreso */}
        <ProgressBar currentStep={currentStep} totalSteps={TOTAL_STEPS} />

        {/* Mensajes */}
        {successMessage && (
          <div className="mb-6 p-4 bg-green-100 border-l-4 border-green-500 rounded flex items-start gap-3 animate-in fade-in slide-in-from-top">
            <CheckCircle2 size={20} className="text-green-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="text-green-800 font-medium">{successMessage}</p>
            </div>
          </div>
        )}

        {errorMessage && (
          <div className="mb-6 p-4 bg-red-100 border-l-4 border-red-500 rounded flex items-start gap-3 animate-in fade-in slide-in-from-top">
            <AlertCircle size={20} className="text-red-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="text-red-800 font-medium">{errorMessage}</p>
            </div>
          </div>
        )}

        {/* Contenido del paso */}
        {currentStep === 1 && (
          <Step1
            country={visit.country || ''}
            format={visit.format || ''}
            store={visit.store || ''}
            email={visit.visit_email || ''}
            onCountryChange={(value) => {
              clearError('country')
              setVisitField('country', value)
            }}
            onFormatChange={(value) => {
              clearError('format')
              setVisitField('format', value)
            }}
            onStoreChange={(value) => {
              clearError('store')
              setVisitField('store', value)
            }}
            onEmailChange={(value) => {
              clearError('visit_email')
              setVisitField('visit_email', value)
            }}
            onNext={handleStep1Next}
            errors={{
              country: errors.country || '',
              format: errors.format || '',
              store: errors.store || '',
              email: errors.visit_email || '',
            }}
            isLoading={isLoading}
          />
        )}

        {currentStep > 1 && currentStep < 9 && (
          <StepForm
            stepNumber={currentStep - 1}
            data={stepData[currentStep] || {}}
            onUpdate={(data) => {
              setStepData({ ...stepData, [currentStep]: data })
            }}
            onNext={handleNextStep}
            onPrevious={handlePreviousStep}
          />
        )}

        {currentStep === 9 && (
          <Step9
            data={stepData[9] || {}}
            allStepData={stepData}
            onUpdate={(data) => {
              setStepData({ ...stepData, 9: data })
            }}
            onPrevious={handlePreviousStep}
            onSubmit={handleFormSubmit}
            isSubmitting={isSubmittingForm}
          />
        )}
      </div>
    </div>
  )
}
