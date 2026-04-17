// Types para el formulario de visitas

export interface Country {
  code: string
  country_code: string
  country: string
}

export interface Store {
  nombre: string
  pais_tienda: string
  pais: string
  zona: string
  email: string
  ubicacion: string
}

export interface Visit {
  id_visita: string
  country: string
  format: string
  store: string
  visit_email: string
  start_datetime: string
  end_datetime?: string
  ip_address: string
  bv_pais: string
  id_sugar_tienda: string
  store_email?: string
  lider_zona?: string
  ubicacion?: string
  lat?: number
  lon?: number

  // Preguntas
  preg_01?: string
  preg_03?: string
  preg_05?: string
  preg_06?: string
  preg_09?: string
  preg_10?: string
  preg_11?: string
  preg_12?: string
  preg_13?: string
  preg_14?: string
  preg_15?: string
  preg_21?: string
  preg_23?: string
  preg_24?: string
  preg_25?: string
  preg_26?: string
  preg_27?: string
  preg_28?: string
  preg_29?: string
  preg_30?: string
  preg_31?: string
  preg_35?: string
  preg_36?: string
  preg_37?: string
  preg_39?: string
  preg_40?: string
  preg_41?: string
  preg_44?: string
  preg_45?: string
  preg_47?: string
  preg_48?: string
  preg_49?: string
  preg_55?: string
  preg_56?: string
  preg_57?: string
  preg_58?: string
  preg_59?: string
  preg_60?: string
  preg_61?: string
  preg_62?: string
  preg_63?: string
  preg_64?: string
  preg_65?: string
  preg_66?: string
  preg_67?: string
  preg_69?: string
  preg_70?: string
  preg_71?: string
  preg_72?: string
  preg_73?: string
  preg_74?: string
  preg_76?: string
  preg_77?: string
  preg_78?: string
  preg_79?: string
  preg_80?: string
  preg_81?: string
  preg_82?: string
  preg_83?: string
  preg_84?: string
  preg_85?: string
  preg_86?: string
  preg_88?: string
  preg_89?: string
  preg_90?: string
  preg_91?: string
  preg_92?: string
  preg_93?: string
  preg_94?: string
  preg_95?: string
  preg_96?: string
  preg_97?: string
  preg_98?: string
  preg_99?: string
  preg_101?: string
  preg_102?: string
  preg_103?: string
  preg_104?: string
  preg_105?: string
  preg_106?: string
  preg_107?: string
  preg_108?: string
  preg_109?: string
  preg_110?: string
  preg_111?: string
  preg_112?: string

  // Observaciones
  obs_01?: string
  obs_02?: string
  obs_03?: string
  obs_04?: string
  obs_05?: string
  obs_06?: string
  obs_07?: string
}

export interface Step {
  id: number
  title: string
  description: string
}

export interface FormErrors {
  [key: string]: string
}
