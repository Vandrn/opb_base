import axios, { AxiosInstance } from 'axios'
import { Country, Store, Visit } from '@/types'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

class VisitService {
  private api: AxiosInstance

  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })
  }

  /**
   * Obtener lista de países
   */
  async getCountries(): Promise<Country[]> {
    try {
      const response = await this.api.get('/countries')
      return response.data.data || []
    } catch (error) {
      console.error('Error fetching countries:', error)
      throw error
    }
  }

  /**
   * Obtener tiendas por país y formato
   */
  async getStores(country: string, format: string): Promise<Store[]> {
    try {
      const response = await this.api.get('/stores', {
        params: { country, format },
      })
      return response.data.data || []
    } catch (error) {
      console.error('Error fetching stores:', error)
      throw error
    }
  }

  /**
   * Crear nueva visita
   */
  async createVisit(data: Partial<Visit>): Promise<{ id_visita: string; data: Visit }> {
    try {
      const response = await this.api.post('/visits', data)
      return {
        id_visita: response.data.visit_id,
        data: response.data.data,
      }
    } catch (error) {
      console.error('Error creating visit:', error)
      throw error
    }
  }

  /**
   * Obtener visita existente
   */
  async getVisit(id: string): Promise<Visit> {
    try {
      const response = await this.api.get(`/visits/${id}`)
      return response.data.data
    } catch (error) {
      console.error('Error fetching visit:', error)
      throw error
    }
  }

  /**
   * Actualizar visita (guardado por pasos)
   */
  async updateVisit(id: string, data: Partial<Visit>): Promise<void> {
    try {
      await this.api.patch(`/visits/${id}`, data)
    } catch (error) {
      console.error('Error updating visit:', error)
      throw error
    }
  }
}

export default new VisitService()
