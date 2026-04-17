import React from 'react';
import { Question } from '../utils/questionsUtil';

interface QuestionCardProps {
  question: Question;
  value: string | number | boolean | undefined;
  onChange: (value: string | number | boolean) => void;
  disabled?: boolean;
}

export const QuestionCard: React.FC<QuestionCardProps> = ({
  question,
  value,
  onChange,
  disabled = false
}) => {
  const isSiNo = question.si_no === '1' || question.si_no === 1;

  return (
    <div className="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
      <label className="block text-sm font-semibold text-gray-800 mb-3">
        <span className="text-yellow-500">#</span> {question.pregunta}
      </label>

      {isSiNo ? (
        <div className="flex gap-3">
          <button
            onClick={() => onChange(true)}
            disabled={disabled}
            className={`flex-1 px-4 py-2 rounded font-medium transition-all ${
              value === true
                ? 'bg-green-500 text-white shadow-md'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            } disabled:opacity-50 disabled:cursor-not-allowed`}
          >
            ✓ Sí
          </button>
          <button
            onClick={() => onChange(false)}
            disabled={disabled}
            className={`flex-1 px-4 py-2 rounded font-medium transition-all ${
              value === false
                ? 'bg-red-500 text-white shadow-md'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            } disabled:opacity-50 disabled:cursor-not-allowed`}
          >
            ✗ No
          </button>
        </div>
      ) : (
        <textarea
          value={value || ''}
          onChange={(e) => onChange(e.target.value)}
          disabled={disabled}
          placeholder="Ingrese observación o comentario..."
          className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-yellow-400 focus:border-transparent disabled:bg-gray-100"
          rows={3}
        />
      )}
    </div>
  );
};
