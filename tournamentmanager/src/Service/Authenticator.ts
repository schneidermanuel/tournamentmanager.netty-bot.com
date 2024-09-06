import { Injectable } from "@angular/core";
import { CookieService } from "ngx-cookie-service";

@Injectable({
  providedIn: 'root'
})
export class Authenticator {
  LogOut() {
    this.token = "";
    this.inited = false;
    this.cookieService.delete("token");
  }
  private token: String = "";
  private inited: boolean = false;

  constructor(private cookieService: CookieService) {
    this.token = this.cookieService.get("token");
  }

  public IsAuthenticated(): boolean {
    if (this.token) {
      return true;
    }
    return false;
  }

}
