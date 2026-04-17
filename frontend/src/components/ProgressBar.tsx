import React from 'react'

interface ProgressBarProps {
  currentStep: number
  totalSteps: number
}

export function ProgressBar({ currentStep, totalSteps }: ProgressBarProps) {
  const percentage = (currentStep / totalSteps) * 100

  return (
    <div className="mb-8">
      <div className="flex justify-between items-center mb-2">
        <h2 className="text-sm font-semibold text-brand-dark">Progreso</h2>
        <span className="text-xs text-gray-600">{currentStep} de {totalSteps}</span>
      </div>
      <div className="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
        <div
          className="h-full bg-gradient-to-r from-brand-yellow to-brand-yellow/80 transition-all duration-300 ease-out rounded-full shadow-md"
          style={{ width: `${percentage}%` }}
        />
      </div>

      {/* Mini indicadores de pasos */}
      <div className="flex gap-1 mt-4 flex-wrap">
        {Array.from({ length: totalSteps }).map((_, i) => (
          <div
            key={i + 1}
            className={`flex-1 h-2 rounded-full transition-colors ${
              i + 1 <= currentStep
                ? 'bg-brand-yellow'
                : 'bg-gray-300'
            }`}
          />
        ))}
      </div>
    </div>
  )
}
