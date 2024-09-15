import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManagetournamentviewComponent } from './managetournamentview.component';

describe('ManagetournamentviewComponent', () => {
  let component: ManagetournamentviewComponent;
  let fixture: ComponentFixture<ManagetournamentviewComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManagetournamentviewComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ManagetournamentviewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
