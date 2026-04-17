import React, { useState } from 'react';
import { CheckCircle } from 'lucide-react';

interface Step9Props {
  data: Record<string, any>;
  onUpdate: (stepData: Record<string, any>) => void;
  onPrevious: () => void;
  onSubmit: () => void;
  isSubmitting?: boolean;
}

export const Step9: React.FC<Step9Props> = ({
  data,
  onUpdate,
  onPrevious,
  onSubmit,
  isSubmitting = false
}) => {
  const [observations, setObservations] = useState(data?.observations || '');
  const [actionPlan, setActionPlan] = useState(data?.actionPlan || '');

  const handleUpdate = () => {
    onUpdate({
      observations,
      actionPlan
    });
  };

  const handleSubmit = () => {
    handleUpdate();
    onSubmit();
  };

  const handlePrevious = () => {
    handleUpdate();
    onPrevious();
  };

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-yellow-400 to-yellow-600 p-6 rounded-lg mb-8 text-gray-800">
          <h2 className="text-2xl font-bold mb-2">Paso 9: Observaciones & Plan de Acción</h2>
          <p className="text-sm opacity-90">
            Finaliza el formulario con observaciones y plan de mejora
          </p>
        </div>

        {/* Progress Bar */}
        <div className="mb-8">
          <div className="flex justify-between mb-2 text-sm text-gray-600">
            <span>Paso 9 de 9</span>
            <span>100%</span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div className="bg-yellow-500 h-2 rounded-full w-full" />
          </div>
        </div>

        {/* Form */}
        <div className="space-y-6 mb-8">
          {/* Observations */}
          <div className="bg-white p-6 rounded-lg border border-gray-200">
            <label className="block text-sm font-semibold text-gray-800 mb-3">
              <span className="text-yellow-500">★</span> Observaciones Generales
            </label>
            <textarea
              value={observations}
              onChange={(e) => setObservations(e.target.value)}
              placeholder="Describe observaciones de la visita, hallazgos importantes, áreas de mejora, fortalezas identificadas..."
              className="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"
              rows={6}
            />
            <p className="text-xs text-gray-500 mt-2">
              {observations.length} caracteres
            </p>
          </div>

          {/* Action Plan */}
          <div className="bg-white p-6 rounded-lg border border-gray-200">
            <label className="block text-sm font-semibold text-gray-800 mb-3">
              <span className="text-yellow-500">★</span> Plan de Acción
            </label>
            <textarea
              value={actionPlan}
              onChange={(e) => setActionPlan(e.target.value)}
              placeholder="Describe acciones específicas a realizar para mejorar. Incluye: qué, quién, cuándo..."
              className="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"
              rows={6}
            />
            <p className="text-xs text-gray-500 mt-2">
              {actionPlan.length} caracteres
            </p>
          </div>

          {/* Summary */}
          <div className="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
            <div className="flex items-start gap-3">
              <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0 mt-1" />
              <div>
                <h3 className="font-semibold text-gray-800 mb-2">Resumen de la Visita</h3>
                <ul className="text-sm text-gray-700 space-y-1">
                  <li>✓ Formulario completado en 9 pasos</li>
                  <li>✓ Todas las áreas de evaluación incluidas</li>
                  <li>✓ Observaciones y plan registrados</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        {/* Navigation */}
        <div className="flex gap-4 sticky bottom-6">
          <button
            onClick={handlePrevious}
            disabled={isSubmitting}
            className="flex-1 px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg hover:bg-gray-500 transition disabled:opacity-50"
          >
            ← Atrás
          </button>
          <button
            onClick={handleSubmit}
            disabled={isSubmitting}
            className="flex-1 px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition disabled:opacity-50 flex items-center justify-center gap-2"
          >
            {isSubmitting ? (
              <>
                <span className="animate-spin">⟳</span> Guardando...
              </>
            ) : (
              <>
                <CheckCircle className="w-5 h-5" /> Enviar Formulario
              </>
            )}
          </button>
        </div>
      </div>
    </div>
  );
};
