import React, { useEffect, useState } from 'react'
import { Country, Store } from '@/types'
import visitService from '@/services/api'
import { AlertCircle, Loader } from 'lucide-react'

interface Step1Props {
  country: string
  format: string
  store: string
  email: string
  onCountryChange: (value: string) => void
  onFormatChange: (value: string) => void
  onStoreChange: (value: string) => void
  onEmailChange: (value: string) => void
  onNext: () => void
  errors: Record<string, string>
  isLoading: boolean
}

const FORMATS = ['ADOC', 'PAR2', 'CAT', 'TNF', 'HP', 'CG', 'Vans']

export function Step1({
  country,
  format,
  store,
  email,
  onCountryChange,
  onFormatChange,
  onStoreChange,
  onEmailChange,
  onNext,
  errors,
  isLoading,
}: Step1Props) {
  const [countries, setCountries] = useState<Country[]>([])
  const [stores, setStores] = useState<Store[]>([])
  const [loadingCountries, setLoadingCountries] = useState(true)
  const [loadingStores, setLoadingStores] = useState(false)

  useEffect(() => {
    const loadCountries = async () => {
      try {
        const data = await visitService.getCountries()
        setCountries(data)
      } catch (error) {
        console.error('Error loading countries:', error)
      } finally {
        setLoadingCountries(false)
      }
    }

    loadCountries()
  }, [])

  // Cargar tiendas cuando cambia país o formato
  useEffect(() => {
    if (country && format) {
      const loadStores = async () => {
        setLoadingStores(true)
        try {
          const data = await visitService.getStores(country, format)
          setStores(data)
        } catch (error) {
          console.error('Error loading stores:', error)
          setStores([])
        } finally {
          setLoadingStores(false)
        }
      }

      loadStores()
    }
  }, [country, format])

  const isFormValid = country && format && store && email && !errors.email

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (isFormValid) {
      onNext()
    }
  }

  const validateEmail = (value: string) => {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/
    onEmailChange(value)
  }

  return (
    <div className="w-full max-w-2xl mx-auto p-4 md:p-8">
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Sección de País */}
        <div className="bg-white rounded-lg border-2 border-brand-yellow p-6 shadow-sm">
          <label className="block text-lg font-semibold text-brand-dark mb-4">
            1. Elige tu país <span className="text-red-500">*</span>
          </label>

          {loadingCountries ? (
            <div className="flex justify-center py-4">
              <Loader className="animate-spin text-brand-yellow" size={24} />
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              {countries.map((c) => (
                <label key={c.code} className="flex items-center gap-3 cursor-pointer p-3 rounded hover:bg-brand-yellow/5 transition">
                  <input
                    type="radio"
                    name="country"
                    value={c.country}
                    checked={country === c.country}
                    onChange={(e) => onCountryChange(e.target.value)}
                    className="w-4 h-4 cursor-pointer accent-brand-yellow"
                  />
                  <span className="text-sm md:text-base font-medium text-brand-dark">{c.country}</span>
                </label>
              ))}
            </div>
          )}

          {errors.country && (
            <div className="mt-2 flex items-center gap-2 text-red-500 text-sm">
              <AlertCircle size={16} />
              {errors.country}
            </div>
          )}
        </div>

        {/* Sección de Formato */}
        <div className="bg-white rounded-lg border-2 border-brand-yellow p-6 shadow-sm">
          <label className="block text-lg font-semibold text-brand-dark mb-4">
            2. Formato <span className="text-red-500">*</span>
          </label>

          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            {FORMATS.map((fmt) => (
              <label key={fmt} className="flex items-center gap-3 cursor-pointer p-3 rounded border-2 border-transparent hover:border-brand-yellow transition">
                <input
                  type="radio"
                  name="format"
                  value={fmt}
                  checked={format === fmt}
                  onChange={(e) => onFormatChange(e.target.value)}
                  className="w-4 h-4 cursor-pointer accent-brand-yellow"
                />
                <span className="text-sm font-medium text-brand-dark">{fmt}</span>
              </label>
            ))}
          </div>

          {errors.format && (
            <div className="mt-2 flex items-center gap-2 text-red-500 text-sm">
              <AlertCircle size={16} />
              {errors.format}
            </div>
          )}
        </div>

        {/* Sección de Tienda */}
        <div className="bg-white rounded-lg border-2 border-brand-yellow p-6 shadow-sm">
          <label className="block text-lg font-semibold text-brand-dark mb-4">
            3. Selecciona tu tienda <span className="text-red-500">*</span>
          </label>

          {loadingStores ? (
            <div className="flex justify-center py-4">
              <Loader className="animate-spin text-brand-yellow" size={24} />
            </div>
          ) : (
            <select
              value={store}
              onChange={(e) => onStoreChange(e.target.value)}
              disabled={!country || !format}
              className="w-full px-4 py-3 border-2 border-brand-dark/20 rounded-lg focus:outline-none focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/30 disabled:bg-gray-100 disabled:cursor-not-allowed transition"
            >
              <option value="">-- Selecciona una Tienda --</option>
              {stores.map((s) => (
                <option key={s.pais_tienda} value={JSON.stringify(s)}>
                  {s.nombre} - {s.zona}
                </option>
              ))}
            </select>
          )}

          {errors.store && (
            <div className="mt-2 flex items-center gap-2 text-red-500 text-sm">
              <AlertCircle size={16} />
              {errors.store}
            </div>
          )}
        </div>

        {/* Sección de Email */}
        <div className="bg-white rounded-lg border-2 border-brand-yellow p-6 shadow-sm">
          <label className="block text-lg font-semibold text-brand-dark mb-4">
            4. Correo Electrónico (de quien realiza la evaluación) <span className="text-red-500">*</span>
          </label>

          <input
            type="email"
            value={email}
            onChange={(e) => validateEmail(e.target.value)}
            placeholder="ejemplo@correo.com"
            className="w-full px-4 py-3 border-2 border-brand-dark/20 rounded-lg focus:outline-none focus:border-brand-yellow focus:ring-2 focus:ring-brand-yellow/30 transition"
          />

          {errors.email && (
            <div className="mt-2 flex items-center gap-2 text-red-500 text-sm">
              <AlertCircle size={16} />
              {errors.email}
            </div>
          )}
        </div>

        {/* Botón Siguiente */}
        <div className="flex justify-end pt-4">
          <button
            type="submit"
            disabled={!isFormValid || isLoading}
            className="px-8 py-3 bg-brand-yellow text-brand-dark font-semibold rounded-lg hover:bg-brand-yellow/90 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center gap-2"
          >
            {isLoading ? (
              <>
                <Loader size={18} className="animate-spin" />
                Guardando...
              </>
            ) : (
              'Siguiente →'
            )}
          </button>
        </div>
      </form>
    </div>
  )
}
