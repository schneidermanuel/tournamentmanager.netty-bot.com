import { Player } from "./Player";

export class Tournament {
  public CreatedDate: string;
  public GuildId: number;
  public GuildName: string;
  public Name: string;
  public OrganiserDisplayName: string;
  public Status: string;
  public Id: number;
  public Players: Player[];
  public DetailsLoaded: boolean;
  public Code: string;
  public CanManage: boolean = false;
  public RoleId: string;
  public RoleName: string;

  public CountPlayers(): number {
    return this.Players.length;
  }
}
