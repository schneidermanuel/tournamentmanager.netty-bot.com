import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-authenticated-view',
  standalone: true,
  imports: [],
  templateUrl: './authenticated-view.component.html',
  styleUrl: './authenticated-view.component.css'
})
export class AuthenticatedViewComponent implements OnInit {

  constructor(private route: ActivatedRoute, private cookieService: CookieService, private router: Router) { }

  ngOnInit(): void {
    let token = this.route.snapshot.paramMap.get('token');
    if (token != null) {
      this.cookieService.set("token", token.toString(), 31, "/");
    }
    this.router.navigate(["/"])
  }

}
