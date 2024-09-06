import { NgIf, NgStyle } from '@angular/common';
import { Component, HostListener, OnInit } from '@angular/core';
import { Authenticator } from '../../Service/Authenticator';

@Component({
  selector: 'app-main-view',
  standalone: true,
  imports: [
    NgStyle,
    NgIf
  ],
  templateUrl: './main-view.component.html',
  styleUrl: './main-view.component.css'
})
export class MainViewComponent implements OnInit {
  public isAuthenticated: boolean = false;

  constructor(private authenticator: Authenticator) { }
  gradientStyle = '';

  @HostListener('mousemove', ['$event'])
  onMouseMove(event: MouseEvent) {
    const x = event.clientX;
    const y = event.clientY;
    this.gradientStyle = `radial-gradient(circle at ${x}px ${y}px, var(--tw-gradient-from), var(--tw-gradient-to))`;
  }

  ngOnInit() {
    this.isAuthenticated = this.authenticator.IsAuthenticated();
    this.gradientStyle = `radial-gradient(circle at center, var(--tw-gradient-from), var(--tw-gradient-to))`;
  }
  public LogOut(): void {
    this.authenticator.LogOut();
    this.isAuthenticated = false;
  }
}
