import { Injectable } from "@angular/core";
import { CookieService } from "ngx-cookie-service";
import { Api } from "./Api";
import { NotificationService } from "./NotificationService";
import { User } from "../Data/User";
import { GlobalState } from "./GlobalState";

@Injectable({
  providedIn: 'root'
})
export class Authenticator {
  private token: string;
  ;
  LogOut() {
    this.token = "";
    this.globalState.User = null;
    this.cookieService.delete("token");
  }

  constructor(private cookieService: CookieService, private notificationService: NotificationService, private api: Api, private globalState: GlobalState) {
    this.token = this.cookieService.get("token");
  }

  public async IsAuthenticated(): Promise<boolean> {
    if (!this.token) {
      this.globalState.IsInited = true;
      return false;
    }
    if (!this.globalState.IsInited) {
      let result = await this.api.SendGetRequest("Login/me");
      if (!result.Manage) {
        this.cookieService.delete("token");
        this.notificationService.ShowWarnung("Sorry, you can't access Netty-Tournaments at the moment")
      }
      this.globalState.User = new User();
      this.globalState.User.UserId = result.UserId;
      this.globalState.User.Username = result.DisplayName;
      this.globalState.User.AvatarUrl = result.AvatarUrl;
      this.notificationService.ShowMessage("Welcome, " + this.globalState.User.Username);
      this.globalState.IsInited = true;
    }
    if (this.token) {
      return true;
    }
    return false;
  }

}
