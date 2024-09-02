import { NgStyle } from '@angular/common';
import { Component, HostListener, OnInit } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-main-view',
  standalone: true,
  imports: [NgStyle],
  templateUrl: './main-view.component.html',
  styleUrl: './main-view.component.css'
})
export class MainViewComponent implements OnInit {

  constructor(private cookieService: CookieService) { }
  gradientStyle = '';

  @HostListener('mousemove', ['$event'])
  onMouseMove(event: MouseEvent) {
    const x = event.clientX;
    const y = event.clientY;
    this.gradientStyle = `radial-gradient(circle at ${x}px ${y}px, var(--tw-gradient-from), var(--tw-gradient-to))`;
  }

  ngOnInit() {
    this.gradientStyle = `radial-gradient(circle at center, var(--tw-gradient-from), var(--tw-gradient-to))`;
    let token = this.cookieService.get("token");
    console.log(token);
  }
}
