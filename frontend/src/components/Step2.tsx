import React, { useEffect, useState } from 'react';
import { QuestionCard } from './QuestionCard';
import { getQuestionsByArea, getAreaName } from '../utils/questionsUtil';
import { Question } from '../utils/questionsUtil';

interface Step2Props {
  format: string;
  data: Record<string, any>;
  onUpdate: (stepData: Record<string, any>) => void;
  onNext: () => void;
  onPrevious: () => void;
}

export const Step2: React.FC<Step2Props> = ({
  format,
  data,
  onUpdate,
  onNext,
  onPrevious
}) => {
  const [questions, setQuestions] = useState<Question[]>([]);
  const [responses, setResponses] = useState<Record<string, any>>(data || {});

  useEffect(() => {
    const area1Questions = getQuestionsByArea('1', format);
    setQuestions(area1Questions);
  }, [format]);

  const handleQuestionChange = (questionId: string, value: any) => {
    const updated = { ...responses, [questionId]: value };
    setResponses(updated);
    onUpdate(updated);
  };

  const handleNext = () => {
    onUpdate(responses);
    onNext();
  };

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="bg-gradient-to-r from-yellow-400 to-yellow-600 p-6 rounded-lg mb-8 text-gray-800">
          <h2 className="text-2xl font-bold mb-2">Paso 2: {getAreaName('1')}</h2>
          <p className="text-sm opacity-90">
            {questions.length} preguntas • Formato: <span className="font-semibold">{format}</span>
          </p>
        </div>

        {/* Questions */}
        <div className="space-y-4 mb-8">
          {questions.length > 0 ? (
            questions.map((question) => (
              <QuestionCard
                key={question.id_pregunta}
                question={question}
                value={responses[question.id_pregunta]}
                onChange={(value) => handleQuestionChange(question.id_pregunta, value)}
              />
            ))
          ) : (
            <div className="bg-white p-6 rounded-lg text-center text-gray-500">
              No hay preguntas disponibles para este formato
            </div>
          )}
        </div>

        {/* Navigation */}
        <div className="flex gap-4">
          <button
            onClick={onPrevious}
            className="flex-1 px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg hover:bg-gray-500 transition"
          >
            ← Atrás
          </button>
          <button
            onClick={handleNext}
            className="flex-1 px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition"
          >
            Siguiente →
          </button>
        </div>
      </div>
    </div>
  );
};
