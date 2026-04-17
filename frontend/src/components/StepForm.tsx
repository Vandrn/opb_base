import React, { useEffect, useState } from 'react';
import { getSectionById, Section } from '../utils/evaluationSections';

interface StepFormProps {
  stepNumber: number;
  data: Record<string, any>;
  onUpdate: (stepData: Record<string, any>) => void;
  onNext: () => void;
  onPrevious: () => void;
}

export const StepForm: React.FC<StepFormProps> = ({
  stepNumber,
  data,
  onUpdate,
  onNext,
  onPrevious
}) => {
  const [section, setSection] = useState<Section | undefined>(undefined);
  const [responses, setResponses] = useState<Record<string, any>>(data || {});

  useEffect(() => {
    const currentSection = getSectionById(stepNumber);
    setSection(currentSection);
  }, [stepNumber]);

  const handleYesNoChange = (questionId: string, value: boolean) => {
    const updated = { ...responses, [questionId]: value };
    setResponses(updated);
  };

  const handleLikertChange = (questionId: string, value: number) => {
    const updated = { ...responses, [questionId]: value };
    setResponses(updated);
  };

  const handleObservationsChange = (value: string) => {
    const updated = { ...responses, [`observations_${stepNumber}`]: value };
    setResponses(updated);
  };

  const handleNext = () => {
    onUpdate(responses);
    onNext();
  };

  const handlePrevious = () => {
    onUpdate(responses);
    onPrevious();
  };

  if (!section) {
    return <div>Cargando...</div>;
  }

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-yellow-400 to-yellow-600 p-6 rounded-lg mb-8 text-gray-800">
          <h2 className="text-2xl font-bold mb-2">{section.title}</h2>
          <p className="text-sm opacity-90">Paso {stepNumber} de 8</p>
        </div>

        {/* Progress Bar */}
        <div className="mb-8">
          <div className="flex justify-between mb-2 text-sm text-gray-600">
            <span>Paso {stepNumber} de 8</span>
            <span>{Math.round(((stepNumber - 1) / 8) * 100)}%</span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div
              className="bg-yellow-500 h-2 rounded-full transition-all duration-300"
              style={{ width: `${((stepNumber - 1) / 8) * 100}%` }}
            />
          </div>
        </div>

        {/* Questions */}
        <div className="space-y-6 mb-8">
          {/* Preguntas Sí/No */}
          {section.questionsYesNo.length > 0 && (
            <div className="bg-white rounded-lg p-6 shadow">
              <h3 className="text-lg font-semibold text-gray-800 mb-4">Preguntas Sí/No</h3>
              <div className="space-y-4">
                {section.questionsYesNo.map((question) => (
                  <div key={question.id} className="border-b pb-4 last:border-b-0">
                    <p className="text-gray-700 font-medium mb-3">{question.text}</p>
                    <div className="flex gap-6">
                      <label className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="radio"
                          name={question.id}
                          checked={responses[question.id] === true}
                          onChange={() => handleYesNoChange(question.id, true)}
                          className="w-4 h-4"
                        />
                        <span className="text-gray-600">Sí</span>
                      </label>
                      <label className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="radio"
                          name={question.id}
                          checked={responses[question.id] === false}
                          onChange={() => handleYesNoChange(question.id, false)}
                          className="w-4 h-4"
                        />
                        <span className="text-gray-600">No</span>
                      </label>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Preguntas Likert */}
          {section.questionsLikert.length > 0 && (
            <div className="bg-white rounded-lg p-6 shadow">
              <h3 className="text-lg font-semibold text-gray-800 mb-4">Evaluación (1: Bajo → 5: Alto)</h3>
              <div className="space-y-4">
                {section.questionsLikert.map((question) => (
                  <div key={question.id} className="border-b pb-4 last:border-b-0">
                    <p className="text-gray-700 font-medium mb-3">{question.text}</p>
                    <div className="flex gap-2">
                      {[1, 2, 3, 4, 5].map((value) => (
                        <label key={value} className="flex items-center cursor-pointer">
                          <input
                            type="radio"
                            name={question.id}
                            value={value}
                            checked={responses[question.id] === value}
                            onChange={() => handleLikertChange(question.id, value)}
                            className="w-4 h-4"
                          />
                          <span className="ml-2 text-gray-600">{value}</span>
                        </label>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Observaciones */}
          <div className="bg-white rounded-lg p-6 shadow">
            <label className="block">
              <p className="text-lg font-semibold text-gray-800 mb-3">{section.observationsLabel}</p>
              <textarea
                value={responses[`observations_${stepNumber}`] || ''}
                onChange={(e) => handleObservationsChange(e.target.value)}
                placeholder="Escribe tus observaciones aquí..."
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                rows={5}
              />
            </label>
          </div>
        </div>

        {/* Navigation */}
        <div className="flex gap-4 sticky bottom-6">
          <button
            onClick={handlePrevious}
            disabled={stepNumber === 1}
            className="flex-1 px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg hover:bg-gray-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            ← Atrás
          </button>
          <button
            onClick={handleNext}
            disabled={stepNumber === 8}
            className="flex-1 px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Siguiente →
          </button>
        </div>
      </div>
    </div>
  );
};
