// Utility: Filter questions by area and format
import questions from '../../data/questions.json';

export interface Question {
  id_pregunta: string;
  id_area: string;
  incluir: string;
  si_no: string;
  pregunta: string;
  adoc: string;
  par2: string;
  hp: string;
  cat: string;
  tnf: string;
  cg: string;
  Vans: string | null;
}

export const getQuestionsByArea = (areaId: string, format: string): Question[] => {
  return questions
    .filter(q => q.id_area === areaId && q.incluir === '1')
    .filter(q => {
      const formatValue = q[format as keyof Question];
      return formatValue === '1' || formatValue === 1;
    })
    .sort((a, b) => parseInt(a.id_pregunta) - parseInt(b.id_pregunta));
};

export const getAreaName = (areaId: string): string => {
  const areas: Record<string, string> = {
    '1': 'Personal & Uniforme',
    '2': 'Servicio al Cliente',
    '3': 'Exhibición de Producto',
    '4': 'Infraestructura & Ambiente',
    '5': 'Bodega',
    '6': 'Conocimiento ADOCKERS',
    '7': 'Tecnología'
  };
  return areas[areaId] || 'Área Unknown';
};
