import { Injectable } from "@angular/core";
import { CookieService } from "ngx-cookie-service";
import { Api } from "./Api";

@Injectable({
  providedIn: 'root'
})
export class Authenticator {
  private token: String = "";
  private manageAllowed: boolean;
  private inited: boolean = false;

  GetToken() {
    return this.token;
  }
  LogOut() {
    this.token = "";
    this.inited = false;
    this.cookieService.delete("token");
  }

  constructor(private cookieService: CookieService, private api: Api) {
    this.token = this.cookieService.get("token");
    api.SendGetRequest("Login/me");
  }

  public IsAuthenticated(): boolean {
    if (this.token) {
      return true;
    }
    return false;
  }

}
