import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-forgot-password',
  template: `
    <div class="forgot-password-container">
      <div class="card">
        <div class="card-header">
          <h2>🔐 Recuperar Contraseña</h2>
          <p>Ingresa tu correo electrónico y te enviaremos un enlace para cambiar tu contraseña.</p>
        </div>
        
        <div class="card-body">
          <form [formGroup]="forgotPasswordForm" (ngSubmit)="onSubmit()">
            <div class="form-group">
              <label for="email">Correo Electrónico</label>
              <input 
                type="email" 
                id="email"
                formControlName="email"
                class="form-control"
                placeholder="ejemplo@correo.com"
                [class.is-invalid]="isFieldInvalid('email')"
              >
              <div class="invalid-feedback" *ngIf="isFieldInvalid('email')">
                <span *ngIf="forgotPasswordForm.get('email')?.errors?.['required']">
                  El correo electrónico es requerido
                </span>
                <span *ngIf="forgotPasswordForm.get('email')?.errors?.['email']">
                  Ingresa un correo electrónico válido
                </span>
              </div>
            </div>
            
            <div class="alert alert-success" *ngIf="successMessage">
              {{ successMessage }}
            </div>
            
            <div class="alert alert-danger" *ngIf="errorMessage">
              {{ errorMessage }}
            </div>
            
            <div class="form-actions">
              <button 
                type="submit" 
                class="btn btn-primary"
                [disabled]="forgotPasswordForm.invalid || isLoading"
              >
                <span *ngIf="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                {{ isLoading ? 'Enviando...' : 'Enviar Correo de Recuperación' }}
              </button>
              
              <button 
                type="button" 
                class="btn btn-secondary"
                (click)="goBack()"
                [disabled]="isLoading"
              >
                Volver al Login
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .forgot-password-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
    }
    
    .card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }
    
    .card-header {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      padding: 30px;
      text-align: center;
    }
    
    .card-header h2 {
      margin: 0 0 10px 0;
      font-size: 28px;
      font-weight: 600;
    }
    
    .card-header p {
      margin: 0;
      opacity: 0.9;
      font-size: 16px;
    }
    
    .card-body {
      padding: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e1e5e9;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }
    
    .form-control:focus {
      outline: none;
      border-color: #dc3545;
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }
    
    .form-control.is-invalid {
      border-color: #dc3545;
    }
    
    .invalid-feedback {
      display: block;
      color: #dc3545;
      font-size: 14px;
      margin-top: 5px;
    }
    
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid transparent;
    }
    
    .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
      color: #155724;
    }
    
    .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }
    
    .form-actions {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      flex: 1;
      min-width: 150px;
    }
    
    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
    }
    
    .btn-primary:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover:not(:disabled) {
      background: #5a6268;
      transform: translateY(-2px);
    }
    
    .spinner-border {
      width: 1rem;
      height: 1rem;
    }
    
    @media (max-width: 480px) {
      .form-actions {
        flex-direction: column;
      }
      
      .btn {
        min-width: auto;
      }
    }
  `]
})
export class ForgotPasswordComponent {
  forgotPasswordForm: FormGroup;
  isLoading = false;
  successMessage = '';
  errorMessage = '';
  
  private apiUrl = 'http://localhost:8000'; // Ajusta según tu configuración

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router
  ) {
    this.forgotPasswordForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });
  }

  isFieldInvalid(fieldName: string): boolean {
    const field = this.forgotPasswordForm.get(fieldName);
    return !!(field && field.invalid && (field.dirty || field.touched));
  }

  onSubmit() {
    if (this.forgotPasswordForm.invalid) {
      return;
    }

    this.isLoading = true;
    this.successMessage = '';
    this.errorMessage = '';

    const email = this.forgotPasswordForm.get('email')?.value;

    this.http.post(`${this.apiUrl}/password/request-reset`, { email })
      .subscribe({
        next: (response: any) => {
          this.isLoading = false;
          if (response.success) {
            this.successMessage = response.message;
            this.forgotPasswordForm.reset();
          } else {
            this.errorMessage = response.message || 'Error al enviar el correo';
          }
        },
        error: (error) => {
          this.isLoading = false;
          console.error('Error:', error);
          
          if (error.status === 404) {
            this.errorMessage = 'No existe una cuenta con este correo electrónico';
          } else if (error.status === 500) {
            this.errorMessage = 'Error interno del servidor. Intenta nuevamente.';
          } else {
            this.errorMessage = error.error?.message || 'Error al enviar el correo de recuperación';
          }
        }
      });
  }

  goBack() {
    this.router.navigate(['/login']);
  }
}