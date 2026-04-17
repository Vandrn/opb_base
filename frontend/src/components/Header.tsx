import React from 'react'

interface HeaderProps {
  currentStep: number
  totalSteps: number
}

export function Header({ currentStep, totalSteps }: HeaderProps) {
  return (
    <header className="bg-gradient-to-r from-brand-dark to-brand-dark/80 text-white py-6 md:py-8 shadow-lg">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl md:text-4xl font-bold">One Playbook</h1>
            <p className="text-brand-yellow text-sm md:text-base mt-1">
              Control de Visitas - Gerente País
            </p>
          </div>
          <div className="text-right">
            <p className="text-brand-yellow text-2xl md:text-3xl font-bold">
              {currentStep}/{totalSteps}
            </p>
            <p className="text-gray-300 text-xs md:text-sm">Paso actual</p>
          </div>
        </div>
      </div>
    </header>
  )
}
