import { Routes } from '@angular/router';
import { MainViewComponent } from '../views/main-view/main-view.component';
import { AuthenticatedViewComponent } from '../views/authenticated-view/authenticated-view.component';

export const routes: Routes = [
  { path: '', component: MainViewComponent },
  { path: 'authenticated/:token', component: AuthenticatedViewComponent }
];
