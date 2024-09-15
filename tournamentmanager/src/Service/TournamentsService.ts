import { Injectable } from "@angular/core";
import { Api } from "./Api";
import { Tournament } from "../Data/Tournament";
import { TournamentList } from "../Data/TournamentList";
import { Player } from "../Data/Player";

@Injectable({
  providedIn: 'root'
})
export class TournamentService {
  constructor(private api: Api) { }

  public async GetTournaments(): Promise<TournamentList> {
    let result = await this.api.SendGetRequest("Tournaments/list");
    var list = new TournamentList;
    result.OpenTournaments.forEach((element: any) => {
      let tournament = this.MapToTournament(element);
      list.OpenTournaments.push(tournament);
    });
    result.OtherTournaments.forEach((element: any) => {
      let tournament = this.MapToTournament(element);
      list.OtherTournaments.push(tournament);
    });
    result.ClosedTournaments.forEach((element: any) => {
      let tournament = this.MapToTournament(element);
      list.ClosedTournaments.push(tournament);
    });
    return list;
  }
  public async GetDetails(identifier: string): Promise<Tournament> {
    let result = await this.api.SendGetRequest("Tournaments/detail/" + identifier);
    let tournament = this.MapToTournament(result);
    return tournament;
  }
  public MapToTournament(input: any): Tournament {
    let tournament = new Tournament();
    tournament.GuildId = input.GuildId;
    tournament.CreatedDate = input.CreatedDate;
    tournament.Name = input.Name;
    tournament.OrganiserDisplayName = input.OrganiserDisplayName;
    tournament.Status = input.Status;
    tournament.Id = input.TournamentId;
    tournament.Code = input.Code;
    tournament.Players = [];
    tournament.CanManage = input.CanManage;
    tournament.GuildName = input.GuildName;
    tournament.DetailsLoaded = input.DetailsLoaded;
    if (input.Users) {
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
      input.Users.forEach((input: any) => {
        tournament.Players.push(this.MapToUser(input))
      });
    }
    return tournament;
  }
  public MapToUser(input: any): Player {
    let player = new Player();
    player.Name = input.PlayerName;
    player.CanHost = input.CanHost;
    player.Friendcode = input.Friendcode;
    player.Timestamp = this.FormatDateTime(input.Timestamp);
    player.DiscordId = input.DiscordId;
    return player;
  }
  private FormatDateTime(datetimeString: string): string {
    const date = new Date(datetimeString.replace(' ', 'T'));
    const options: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'numeric',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    };
    const formatter = new Intl.DateTimeFormat("de-CH", options);
    return formatter.format(date);
  }
}

