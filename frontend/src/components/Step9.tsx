import React, { useState } from 'react';
import { CheckCircle, AlertCircle } from 'lucide-react';
import { EVALUATION_SECTIONS } from '@/utils/evaluationSections';

interface Step9Props {
  data: Record<string, any>;
  allStepData: Record<number, Record<string, any>>;
  onUpdate: (stepData: Record<string, any>) => void;
  onPrevious: () => void;
  onSubmit: () => void;
  isSubmitting?: boolean;
}

export const Step9: React.FC<Step9Props> = ({
  data,
  allStepData,
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

  // Contar respuestas completadas
  const countAnswers = (stepNum: number) => {
    const stepData = allStepData[stepNum + 1] || {};
    return Object.keys(stepData).filter(
      key => stepData[key] !== null && stepData[key] !== undefined && key !== 'observations'
    ).length;
  };

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-yellow-400 to-yellow-600 p-6 rounded-lg mb-8 text-gray-800">
          <h2 className="text-2xl font-bold mb-2">Resumen & Finalización</h2>
          <p className="text-sm opacity-90">
            Revisa el resumen de todas las secciones completadas y agrega observaciones finales
          </p>
        </div>

        {/* Progress Bar */}
        <div className="mb-8">
          <div className="flex justify-between mb-2 text-sm text-gray-600">
            <span>Paso 8 de 8</span>
            <span>100%</span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div className="bg-yellow-500 h-2 rounded-full w-full" />
          </div>
        </div>

        {/* Resumen de Secciones */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
          {EVALUATION_SECTIONS.map((section, idx) => {
            const answers = countAnswers(idx);
            const section_data = allStepData[section.id + 1] || {};
            const obs = section_data[`observations_${section.id}`];

            return (
              <div key={section.id} className="bg-white p-4 rounded-lg border border-gray-200">
                <div className="flex items-start justify-between mb-2">
                  <h3 className="font-semibold text-gray-800 text-sm">{section.title}</h3>
                  <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
                </div>
                <div className="space-y-1 text-xs text-gray-600">
                  {answers > 0 && <p>✓ {answers} preguntas respondidas</p>}
                  {obs && <p>✓ Observaciones añadidas</p>}
                  {!answers && !obs && <p className="text-gray-400">Sin datos</p>}
                </div>
              </div>
            );
          })}
        </div>

        {/* Observations */}
        <div className="bg-white p-6 rounded-lg border border-gray-200 mb-6">
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
        <div className="bg-white p-6 rounded-lg border border-gray-200 mb-6">
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

        {/* Final Summary */}
        <div className="bg-green-50 p-6 rounded-lg border border-green-200 mb-8">
          <div className="flex items-start gap-3">
            <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0 mt-1" />
            <div>
              <h3 className="font-semibold text-gray-800 mb-2">¡Evaluación Completa!</h3>
              <ul className="text-sm text-gray-700 space-y-1">
                <li>✓ Todas las 7 secciones completadas</li>
                <li>✓ Observaciones y plan de acción registrados</li>
                <li>✓ Listo para enviar al servidor</li>
              </ul>
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
