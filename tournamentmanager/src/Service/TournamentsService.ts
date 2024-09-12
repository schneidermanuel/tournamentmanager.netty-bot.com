import { Injectable } from "@angular/core";
import { Api } from "./Api";
import { Tournament } from "../Data/Tournament";
import { TournamentList } from "../Data/TournamentList";

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
  public MapToTournament(input: any): Tournament {
    let tournament = new Tournament();
    tournament.GuildId = input.GuildId;
    tournament.CreatedDate = input.CreatedDate;
    tournament.Name = input.Name;
    tournament.OrganiserDisplayName = input.OrganiserDisplayName;
    tournament.Status = input.Status;
    tournament.Id = input.TournamentId;
    tournament.Players = [];
    return tournament;
  }
}
