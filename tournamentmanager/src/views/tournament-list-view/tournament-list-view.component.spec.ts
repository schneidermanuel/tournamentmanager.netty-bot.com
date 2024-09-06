import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentListViewComponent } from './tournament-list-view.component';

describe('TournamentListViewComponent', () => {
  let component: TournamentListViewComponent;
  let fixture: ComponentFixture<TournamentListViewComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TournamentListViewComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TournamentListViewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
