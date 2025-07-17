import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-reset-password',
  template: `
    <div class="reset-password-container">
      <div class="card">
        <div class="card-header">
          <h2>🔑 Cambiar Contraseña</h2>
          <p *ngIf="tokenValid">Ingresa tu nueva contraseña</p>
          <p *ngIf="!tokenValid && !isLoading">Enlace inválido o expirado</p>
        </div>
        
        <div class="card-body">
          <!-- Loading state -->
          <div *ngIf="isLoading" class="loading-container">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Verificando enlace...</p>
          </div>
          
          <!-- Invalid token message -->
          <div *ngIf="!tokenValid && !isLoading" class="invalid-token">
            <div class="alert alert-danger">
              <h4>⚠️ Enlace Inválido</h4>
              <p>El enlace para cambiar la contraseña es inválido o ha expirado.</p>
              <p>Por favor, solicita un nuevo enlace de recuperación.</p>
            </div>
            <button class="btn btn-primary" (click)="goToForgotPassword()">
              Solicitar Nuevo Enlace
            </button>
          </div>
          
          <!-- Reset password form -->
          <form *ngIf="tokenValid && !isLoading" [formGroup]="resetPasswordForm" (ngSubmit)="onSubmit()">
            <div class="form-group">
              <label for="password">Nueva Contraseña</label>
              <input 
                type="password" 
                id="password"
                formControlName="password"
                class="form-control"
                placeholder="Ingresa tu nueva contraseña"
                [class.is-invalid]="isFieldInvalid('password')"
              >
              <div class="invalid-feedback" *ngIf="isFieldInvalid('password')">
                <span *ngIf="resetPasswordForm.get('password')?.errors?.['required']">
                  La contraseña es requerida
                </span>
                <span *ngIf="resetPasswordForm.get('password')?.errors?.['minlength']">
                  La contraseña debe tener al menos 8 caracteres
                </span>
              </div>
              <small class="form-text text-muted">
                La contraseña debe tener al menos 8 caracteres
              </small>
            </div>
            
            <div class="form-group">
              <label for="password_confirmation">Confirmar Contraseña</label>
              <input 
                type="password" 
                id="password_confirmation"
                formControlName="password_confirmation"
                class="form-control"
                placeholder="Confirma tu nueva contraseña"
                [class.is-invalid]="isFieldInvalid('password_confirmation')"
              >
              <div class="invalid-feedback" *ngIf="isFieldInvalid('password_confirmation')">
                <span *ngIf="resetPasswordForm.get('password_confirmation')?.errors?.['required']">
                  La confirmación de contraseña es requerida
                </span>
                <span *ngIf="resetPasswordForm.get('password_confirmation')?.errors?.['passwordMismatch']">
                  Las contraseñas no coinciden
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
                [disabled]="resetPasswordForm.invalid || isSubmitting"
              >
                <span *ngIf="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                {{ isSubmitting ? 'Cambiando Contraseña...' : 'Cambiar Contraseña' }}
              </button>
              
              <button 
                type="button" 
                class="btn btn-secondary"
                (click)="goToLogin()"
                [disabled]="isSubmitting"
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
    .reset-password-container {
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
      max-width: 500px;
      overflow: hidden;
    }
    
    .card-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    
    .loading-container {
      text-align: center;
      padding: 40px 20px;
    }
    
    .spinner-border {
      width: 3rem;
      height: 3rem;
    }
    
    .invalid-token {
      text-align: center;
      padding: 20px 0;
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
      border-color: #28a745;
      box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
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
    
    .form-text {
      font-size: 14px;
      color: #6c757d;
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
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }
    
    .btn-primary:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
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
export class ResetPasswordComponent implements OnInit {
  resetPasswordForm: FormGroup;
  isLoading = true;
  isSubmitting = false;
  tokenValid = false;
  successMessage = '';
  errorMessage = '';
  token = '';
  
  private apiUrl = 'http://localhost:8000'; // Ajusta según tu configuración

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.resetPasswordForm = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit() {
    // Obtener el token de la URL
    this.route.queryParams.subscribe(params => {
      this.token = params['token'];
      if (this.token) {
        this.verifyToken();
      } else {
        this.isLoading = false;
        this.tokenValid = false;
      }
    });
  }

  passwordMatchValidator(form: FormGroup) {
    const password = form.get('password');
    const passwordConfirmation = form.get('password_confirmation');
    
    if (password && passwordConfirmation && password.value !== passwordConfirmation.value) {
      passwordConfirmation.setErrors({ passwordMismatch: true });
      return { passwordMismatch: true };
    }
    
    return null;
  }

  verifyToken() {
    this.http.post(`${this.apiUrl}/password/verify-token`, { token: this.token })
      .subscribe({
        next: (response: any) => {
          this.isLoading = false;
          if (response.success) {
            this.tokenValid = true;
          } else {
            this.tokenValid = false;
            this.errorMessage = response.message;
          }
        },
        error: (error) => {
          this.isLoading = false;
          this.tokenValid = false;
          console.error('Error verifying token:', error);
          this.errorMessage = 'Error al verificar el enlace';
        }
      });
  }

  isFieldInvalid(fieldName: string): boolean {
    const field = this.resetPasswordForm.get(fieldName);
    return !!(field && field.invalid && (field.dirty || field.touched));
  }

  onSubmit() {
    if (this.resetPasswordForm.invalid) {
      return;
    }

    this.isSubmitting = true;
    this.successMessage = '';
    this.errorMessage = '';

    const formData = {
      token: this.token,
      password: this.resetPasswordForm.get('password')?.value,
      password_confirmation: this.resetPasswordForm.get('password_confirmation')?.value
    };

    this.http.post(`${this.apiUrl}/password/reset`, formData)
      .subscribe({
        next: (response: any) => {
          this.isSubmitting = false;
          if (response.success) {
            this.successMessage = response.message;
            this.resetPasswordForm.reset();
            
            // Redirigir al login después de 3 segundos
            setTimeout(() => {
              this.router.navigate(['/login']);
            }, 3000);
          } else {
            this.errorMessage = response.message || 'Error al cambiar la contraseña';
          }
        },
        error: (error) => {
          this.isSubmitting = false;
          console.error('Error:', error);
          
          if (error.status === 400) {
            this.errorMessage = error.error?.message || 'Token inválido o expirado';
          } else if (error.status === 500) {
            this.errorMessage = 'Error interno del servidor. Intenta nuevamente.';
          } else {
            this.errorMessage = error.error?.message || 'Error al cambiar la contraseña';
          }
        }
      });
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }

  goToForgotPassword() {
    this.router.navigate(['/forgot-password']);
  }
} 