// All evaluation sections with their questions organized by section

export interface Question {
  id: string;
  text: string;
  type: 'si_no' | 'likert';
}

export interface Section {
  id: number;
  title: string;
  questionsYesNo: Question[];
  questionsLikert: Question[];
  observationsLabel: string;
}

export const EVALUATION_SECTIONS: Section[] = [
  {
    id: 1,
    title: 'Evaluación Código de Vestimenta ADOCKER',
    questionsYesNo: [
      { id: '1-1', text: 'Uso de carnet con nombre visible', type: 'si_no' },
      { id: '1-2', text: 'Adockers con su respectivo uniforme y cuidando su imagen personal', type: 'si_no' }
    ],
    questionsLikert: [],
    observationsLabel: 'Observaciones del área de código de vestimenta ADOCKER'
  },
  {
    id: 2,
    title: 'Evaluación Experiencia de Servicio',
    questionsYesNo: [
      { id: '2-1', text: 'El líder se encuentra en la entrada principal de la tienda', type: 'si_no' },
      { id: '2-2', text: '¿Adockers hacen uso de la APP ADOCKY cuando atienden a los clientes en el piso de venta?', type: 'si_no' },
      { id: '2-3', text: '¿Adockers hacen uso de la APP ADOCKY para realizar la representación de inventario?', type: 'si_no' }
    ],
    questionsLikert: [],
    observationsLabel: 'Observaciones del área de Experiencia de Servicio'
  },
  {
    id: 3,
    title: 'Evaluación Exhibición y Planograma',
    questionsYesNo: [
      { id: '3-1', text: 'Exhibición de producto según estándares de OPB: Tallas autorizadas en vitrinas y mesas: Hombre 8 (si se agota 6 o 7), Mujer 7 (si se agota 5 o 6)', type: 'si_no' },
      { id: '3-2', text: 'Encintado cruzado (excepto CAT)', type: 'si_no' },
      { id: '3-3', text: 'Sandalias con modelador acrílico en "S" solo en vitrina o mesas', type: 'si_no' },
      { id: '3-4', text: 'Se respetan las categorías y subcategorías en góndolas', type: 'si_no' },
      { id: '3-5', text: 'Etiqueta de precio ubicada según estándar', type: 'si_no' },
      { id: '3-6', text: 'Etiqueta en buen estado', type: 'si_no' },
      { id: '3-7', text: 'Precio escrito en color negro', type: 'si_no' },
      { id: '3-8', text: 'Producto en perfectas condiciones (sin rasguños, suciedad o desperfectos de fábrica, etc)', type: 'si_no' },
      { id: '3-9', text: 'Las exhibiciones en mesa principal, vitrinas y punta de góndola inician con el zapato en marcha (Salvo botas)', type: 'si_no' },
      { id: '3-10', text: '100% de la Exhibición de productos según colorimetría autorizada: Claro a Oscuro, de izquierda a derecha', type: 'si_no' },
      { id: '3-11', text: 'Área de mujer ubicada al lado derecho de la entrada principal, área de hombre al lado izquierdo, no se encuentran mezclada salvo en zona de descuento', type: 'si_no' },
      { id: '3-12', text: 'Muebles y decoración en buen estado: Mesa principal', type: 'si_no' },
      { id: '3-13', text: 'Repisas', type: 'si_no' },
      { id: '3-14', text: 'Sillas, sillones o taburetes', type: 'si_no' },
      { id: '3-15', text: 'Espejos', type: 'si_no' },
      { id: '3-16', text: 'Mueble de caja', type: 'si_no' },
      { id: '3-17', text: 'Alfombras, lámparas, etc.', type: 'si_no' },
      { id: '3-18', text: 'Respeta y sigue el lineamiento de capacidades de góndola, punta de góndola y punta de colgante o pared', type: 'si_no' },
      { id: '3-19', text: 'Colocan calzado colgante en su respectivo gancho (mujer "w", hombre y niño en gancho en forma de "c" o kiditos)', type: 'si_no' },
      { id: '3-20', text: 'Todo el producto cuenta con sensor de seguridad en un lugar que no daña el producto', type: 'si_no' }
    ],
    questionsLikert: [
      { id: '3-likert-1', text: 'Zona de Ofertas exhibida de forma ordenada y en última repisa de góndolas identificada con cenefa', type: 'likert' }
    ],
    observationsLabel: 'Observaciones del área de Exhibición y Planograma'
  },
  {
    id: 4,
    title: 'Evaluación: Experiencia Sensorial | Visual Merchandising',
    questionsYesNo: [
      { id: '4-1', text: 'Aires acondicionados/ventiladores funcionando correctamente', type: 'si_no' },
      { id: '4-2', text: 'Sala de ventas con 100% de luminarias funcionando', type: 'si_no' },
      { id: '4-3', text: 'Vitrinas de acuerdo a estándar OPB: Vidrio limpio', type: 'si_no' },
      { id: '4-4', text: 'Montajes acorde a planograma y en buen estado', type: 'si_no' },
      { id: '4-5', text: '100% luminarias funcionando', type: 'si_no' },
      { id: '4-6', text: 'Pantallas funcionando', type: 'si_no' },
      { id: '4-7', text: 'Material POP en buen estado: Display', type: 'si_no' },
      { id: '4-8', text: 'Visuales', type: 'si_no' },
      { id: '4-9', text: 'Cabecera de góndola', type: 'si_no' },
      { id: '4-10', text: 'Windows stickers', type: 'si_no' },
      { id: '4-11', text: 'Afiche de vitrina', type: 'si_no' },
      { id: '4-12', text: 'Cajas alzadoras', type: 'si_no' },
      { id: '4-13', text: 'Soporte de acrílico', type: 'si_no' },
      { id: '4-14', text: 'PROPS', type: 'si_no' }
    ],
    questionsLikert: [
      { id: '4-likert-1', text: 'Pintura de Tienda en buen estado Interior/Exterior', type: 'likert' },
      { id: '4-likert-2', text: 'Sala de ventas limpia y ordenada', type: 'likert' },
      { id: '4-likert-3', text: 'La tienda tiene asientos suficientes y en buen estado para prueba de zapatos', type: 'likert' },
      { id: '4-likert-4', text: 'Reproducción de playlist autorizada y con volumen permitido', type: 'likert' },
      { id: '4-likert-5', text: 'Aroma de la tienda agradable', type: 'likert' },
      { id: '4-likert-6', text: 'Rótulo en buen estado (limpio, sin quebraduras e iluminado)', type: 'likert' }
    ],
    observationsLabel: 'Observaciones del área: Experiencia Sensorial | Visual Merchandising'
  },
  {
    id: 5,
    title: 'Evaluación: Bodega y Recepción de Producto',
    questionsYesNo: [
      { id: '5-1', text: 'Cajas con zapato exhibido están debidamente identificadas con botón en bodega (en caso de mesa de entras y vitrinas)', type: 'si_no' },
      { id: '5-2', text: 'Puerta de bodega permanece cerrada', type: 'si_no' }
    ],
    questionsLikert: [],
    observationsLabel: 'Observaciones del área de Bodega y Recepción de Producto'
  },
  {
    id: 6,
    title: 'Evaluación: Operaciones / Ventas',
    questionsYesNo: [
      { id: '6-1', text: 'ADOCKERS conocen las promociones vigentes y están actualizadas', type: 'si_no' }
    ],
    questionsLikert: [],
    observationsLabel: 'Observaciones del área de Operaciones/Ventas'
  },
  {
    id: 7,
    title: 'Evaluación: ¡ADOCKERS A BORDO!',
    questionsYesNo: [],
    questionsLikert: [],
    observationsLabel: 'Observaciones del área ¡ADOCKERS A BORDO!'
  }
];

export const getSectionById = (id: number): Section | undefined => {
  return EVALUATION_SECTIONS.find(section => section.id === id);
};
