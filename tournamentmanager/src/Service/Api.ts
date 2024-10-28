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
        if (response.status == 401) {
          this.cookieService.delete("token");
        }

        this.notificationService.ShowError("Call to server failed with status " + response.status);
        return null;
      })
      .then(data => {
        if (data == null) {
          return null;
        }
        if (data.Message != null) {
          return data.Message;
        }
        this.notificationService.ShowWarnung("Server returned success without data")
        return null;
      });
  }
  public async SendPostRequest(location: string, payload: any): Promise<any> {
    let headers = new Headers();
    headers.set("Authorization", "Bearer " + this.cookieService.get("token"));
    headers.set("Content-Type", "application/json");

    return await fetch(this._backendUrl + location, {
      method: "POST",
      headers: headers,
      body: JSON.stringify(payload)
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
          if (data.Type == "OK") {
            this.notificationService.ShowMessage(data.Message);
          }
          return data.Message;
        }
        this.notificationService.ShowWarnung("Server returned success without data");
        return null;
      });
  }
}
