using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using Game;
using System.Linq;

public class PlayerController : Photon.MonoBehaviour 
{

	public Font font;
	public Font arialFont;
	private PlayerList playerList;

	public Texture2D textureBigBox;
	public Texture2D textureMidBox;
	public Texture2D textureLittleBox;
	public Texture2D textureHeaderBox;
	public Texture2D textureButton;
	public Texture2D textureHoveredButton;
	public Texture2D textureClickedButton;
	public Texture2D textureBackground;

	public Vector2 scrollPosition;
	public Vector2 scrollPositionChat;
	private string message;
	private string roomErrorMessage;
	private GUIStyle button;

	private GUIStyle bigBox;
	private GUIStyle midBox;
	private GUIStyle littleBox;
	private GUIStyle headerLabel;
	private GUIStyle chatLabel;
	private GUIStyle background;
	private GUIStyle roomError;

	void Awake () 
	{

		//Destroy(GameObject.Find("PlayerName"));
		GameObject playerInfoObject = GameObject.Find("PlayerList");
		playerList = playerInfoObject.GetComponent<PlayerList>();

		gameObject.name = PhotonNetwork.player.name;

		if(photonView.isMine)
		{
			AddPlayerList(PhotonNetwork.player);
			photonView.RPC("AddPlayerList", PhotonTargets.Others, PhotonNetwork.player);
		} 

	}

	void Start()
	{
		message = "";

		
	}

	void OnGUI()
	{
		if(photonView.isMine)
		{

			this.InitializeCommonStuffs();

			GUI.Box(new Rect((Screen.width - 960) / 2, (Screen.height - 600) / 2, 960, 600), "",background);
			GUILayout.BeginArea(new Rect((Screen.width - 800) / 2, (Screen.height - 400) / 2, 800, 400),bigBox);
			GUILayout.BeginArea(new Rect(20,40,400,300), "",midBox);
			GUILayout.Label("PLAYERS",headerLabel);
				GUILayout.Space(5);
				scrollPosition = GUILayout.BeginScrollView(scrollPosition, GUILayout.Width(380), GUILayout.Height(170));
				
					foreach(Player player in playerList.players)
					{
						
						GUILayout.BeginHorizontal(GUILayout.Width(350));	
						GUILayout.Label(player.PlayerName,littleBox);

						GUILayout.EndHorizontal();

					} 

				GUILayout.EndScrollView();

			if(PhotonNetwork.isMasterClient && GUILayout.Button("Start Game",button, GUILayout.Width(150), GUILayout.Height(30)))
			{
				if(playerList.players.Count == PhotonNetwork.room.maxPlayers)
					photonView.RPC("StartGame", PhotonTargets.All);
				else
					roomErrorMessage = "Lacking " + (PhotonNetwork.room.maxPlayers - playerList.players.Count) + " player/s.";
			}

			GUILayout.Space(5);
			GUILayout.Label(roomErrorMessage, roomError );

			GUILayout.EndArea();

			GUILayout.BeginArea(new Rect(450,40,300,300), "",midBox);
			GUILayout.Label("CHAT",headerLabel);
			GUILayout.Space(20);

			scrollPositionChat = GUILayout.BeginScrollView(scrollPositionChat, GUILayout.Width(270), GUILayout.Height(160));
			foreach(string mes in playerList.messages)
			{

				GUILayout.Label(mes,chatLabel,GUILayout.Width(200));

			}	
			GUILayout.EndScrollView();
			littleBox.font = arialFont;
			message = GUILayout.TextField(message,40, littleBox  ,GUILayout.Width(200));
			message = message.Replace("\n"[0],' ');



			if((GUILayout.Button("Send",button, GUILayout.Width(100), GUILayout.Height(30)) || (Event.current.type == EventType.KeyUp && Event.current.keyCode == KeyCode.Return) ) && message.Length > 0)
			{
				photonView.RPC("AddChatToList", PhotonTargets.All, PhotonNetwork.player.name +" : " + message);
				message = "";
			}

			GUILayout.EndArea();

			GUILayout.BeginArea(new Rect(30,350,210,40), "");

			GUILayout.BeginHorizontal(GUILayout.Width(350));
			
			if(GUILayout.Button("Return to Lobby",button,GUILayout.Width(200),GUILayout.Height(30)))
			{
				photonView.RPC("RemoveFromPlayerList", PhotonTargets.Others, PhotonNetwork.player.name);
				Destroy(playerList.transform.gameObject);
				
				PhotonNetwork.LeaveRoom();
			}

			GUILayout.EndHorizontal();

			GUILayout.EndArea();

			GUILayout.EndArea();

			

		}
		
	}

	void Update () {
	}

	void OnApplicationQuit()
	{

		photonView.RPC("RemoveFromPlayerList", PhotonTargets.Others, PhotonNetwork.player.name);

	}

	public void InitializeCommonStuffs()
	{

		GUI.skin.font = font;
		
		
		bigBox = new GUIStyle("Box");
		bigBox.normal.background = textureBigBox;
		midBox = new GUIStyle("Box");
		midBox.normal.background = textureMidBox;
		littleBox = new GUIStyle("Box");
		littleBox.normal.background = textureLittleBox;
		littleBox.normal.textColor = Color.black;
		littleBox.margin.left = 10;
		littleBox.alignment = TextAnchor.MiddleLeft;
		headerLabel = new GUIStyle("Box");
		headerLabel.normal.background = textureHeaderBox;
		headerLabel.margin.top = 15;
		chatLabel = new GUIStyle("Box");
		chatLabel.font = arialFont;
		chatLabel.normal.background = textureHeaderBox;
		chatLabel.alignment = TextAnchor.MiddleLeft;

		button = new GUIStyle("Box");
		button.normal.background = textureButton;
		button.alignment = TextAnchor.MiddleCenter;
		button.hover.background = textureHoveredButton;
		button.hover.textColor = Color.white;
		button.active.background = textureClickedButton;
		button.active.textColor = Color.white;
		button.margin.left = 10;

		roomError = new GUIStyle();
		roomError.margin.left = 15;
		roomError.normal.textColor = Color.white;

		background = new GUIStyle("Box");
		background.normal.background = textureBackground;


	}

	#region photon methods

	void OnPhotonSerializeView(PhotonStream stream, PhotonMessageInfo info)
	{
		if (stream.isWriting)
		{
			stream.SendNext(gameObject.name);
		}
		else
		{
			gameObject.name = (string)stream.ReceiveNext();
		}

	
	}

	public void OnLeftRoom()
	{
		
		Application.LoadLevel(DashboardMenu.SceneNameMenu);
	}

	public void OnPhotonPlayerConnected(PhotonPlayer player)
	{

		if(photonView.isMine)
		{
			photonView.RPC("AddPlayerList", player, PhotonNetwork.player);	
			Debug.Log("A player has connected");
		}
	}

	public void OnPhotonPlayerDisconnected(PhotonPlayer player)
	{

		RemoveFromPlayerList(player);

	}

	public void OnDisconnectedFromPhoton()
	{
		Debug.Log("OnDisconnectedFromPhoton");

	}

	#endregion


	#region RPC methods

	[RPC]
	public void StartGame()
	{
		PhotonNetwork.LoadLevel(DashboardMenu.SceneNameBattle);
		
	}

	[RPC]
	public void AddPlayerList(PhotonPlayer player)
	{
		if(playerList.GetPlayer(player.name) == null)
		{
			Player p = new Player(player);
			playerList.players.Add(p);
			playerList.players.Sort((player1, player2)=> player1.PlayerName.CompareTo(player2.PlayerName));	// change to sort on start game
		}
	}

	[RPC]
	void RemoveFromPlayerList(PhotonPlayer player)
	{

		int index = 0;
		foreach(Player p in playerList.players)
		{

			if(p.PlayerName == player.name)
			{

				playerList.players.RemoveAt(index);
				break;

			}
			index++;
		}

	}

	[RPC]
	void AddChatToList(string message)
	{

		playerList.messages.Add(message);

	}

	#endregion
}
