import { Injectable } from "@angular/core";
import { NotificationService } from "./NotificationService";
import { CookieService } from "ngx-cookie-service";

@Injectable({
  providedIn: 'root'
})
export class Api {
  private _backendUrl = "https://api.tournamentmanager.netty-bot.com/";
  constructor(private cookieService: CookieService, private notificationService: NotificationService) { }

  public async SendGetRequest(location: String): Promise<any> {
    let headers = new Headers();
    headers.set("Authorization", "Bearer " + this.cookieService.get("token"))

    return await fetch(this._backendUrl + location, {
      method: "GET",
      headers: headers
    })
      .then(response => {
        if (response.ok) {
          return response.json();
        }

        this.notificationService.ShowError("Call to server failed with status " + response.status);
        return null;
      })
      .then(data => {
        if (data != null) {
          return data.Message;
        }
        this.notificationService.ShowWarnung("Server returned success without data")
        return null;
      });
  }
}
