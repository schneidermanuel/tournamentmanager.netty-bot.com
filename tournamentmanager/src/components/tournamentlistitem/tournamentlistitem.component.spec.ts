import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentlistitemComponent } from './tournamentlistitem.component';

describe('TournamentlistitemComponent', () => {
  let component: TournamentlistitemComponent;
  let fixture: ComponentFixture<TournamentlistitemComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TournamentlistitemComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TournamentlistitemComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
