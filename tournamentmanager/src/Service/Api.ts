import { Injectable } from "@angular/core";
import { Authenticator } from "./Authenticator";
import { NotificationService } from "./NotificationService";

@Injectable({
  providedIn: 'root'
})
export class Api {
  private _backendUrl = "https://api.tournamentmanager.netty-bot.com/";
  constructor(private authenticator: Authenticator, private notificationService: NotificationService) { }

  public async SendGetRequest(location: String): Promise<any> {
    let headers = new Headers();
    headers.set("Authorization", "Bearer " + this.authenticator.GetToken())

    await fetch(this._backendUrl + location, {
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

        }
      })
  }
}
