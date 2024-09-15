import { Routes } from '@angular/router';
import { MainViewComponent } from '../views/main-view/main-view.component';
import { AuthenticatedViewComponent } from '../views/authenticated-view/authenticated-view.component';
import { TournamentListViewComponent } from '../views/tournament-list-view/tournament-list-view.component';

export const routes: Routes = [
  { path: '', component: MainViewComponent },
  { path: 'list', component: TournamentListViewComponent },
  { path: 'authenticated/:token', component: AuthenticatedViewComponent },
  { path: 'manage/:code', component: MainViewComponent },
];
