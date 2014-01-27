using System;
using UnityEngine;
using Random = UnityEngine.Random;
using RPS.Models;
using Game;

public class DashboardMenu : MonoBehaviour
{

	public Font font;
	public Font arialFont;

	public Texture2D textureDashboardBox;
	public Texture2D textureBackground;
	public Texture2D textureButton;
	public Texture2D textureTextbox;
	public Texture2D texturePlusButton;
	public Texture2D textureHoveredButton;
	public Texture2D textureClickedButton;
	public Texture2D textureJoinGameBox;

	private GUIStyle background;
	private GUIStyle dashboardBox;
	private GUIStyle button;
	private GUIStyle textbox;
	private GUIStyle minusButton;
	private GUIStyle plusButton;
	private GUIStyle numberOfPlayersGui;
	private GUIStyle joinGameBox;
	private GUIStyle roomListLabel;

	private string roomName;
	private Vector2 scrollPos;
	private bool connectFailed;
	private int[] numberOfPlayers;
	private int currentNumberOfPlayers;
	private bool isGameReady;
	private string roomNameError;
	public string playerName;
	private string errorOnJoin;

	public static readonly string SceneNameMenu = "Dashboard";
	public static readonly string SceneNameGame = "PlayerRoom";
	public static readonly string SceneNameBattle = "Battle";
	public static readonly string SceneNameSinglePlay = "rps";
	private RPSAudio rpsAudio;

	private Menu currentMenu;
	private User user;

	private GUILayoutOption[] guiLayoutOptions;
	private GUILayoutOption[] guiLayoutOptionsTextbox;
	private GUILayoutOption[] plusMinus;


	#region unity methods

	public void Awake()
	{
		// this makes sure we can use PhotonNetwork.LoadLevel() on the master client and all clients in the same room sync their level automatically
		PhotonNetwork.automaticallySyncScene = true;


		// the following line checks if this client was just created (and not yet online). if so, we connect
		if (PhotonNetwork.connectionStateDetailed == PeerState.PeerCreated)
		{
			// Connect to the photon master-server. We use the settings saved in PhotonServerSettings (a .asset file in this project)
			PhotonNetwork.ConnectUsingSettings("1.0");
		}

		guiLayoutOptions = new GUILayoutOption[2];
		guiLayoutOptionsTextbox = new GUILayoutOption[2];
	}

	void Start()
	{

		rpsAudio = new RPSAudio();

		currentMenu = Menu.SelectionMenu;
		roomName = "";
		playerName = GameObject.Find("PlayerName").transform.GetComponent<PlayerNameContainer>().playerName;
		roomNameError = "";
		scrollPos = Vector2.zero;
		connectFailed = false;
		currentNumberOfPlayers = 0;
		numberOfPlayers = new int[2];
		numberOfPlayers[0] = 2;
		numberOfPlayers[1] = 4;
		//numberOfPlayers[2] = 8;
		//numberOfPlayers[3] = 16;
		//numberOfPlayers[4] = 32;

		isGameReady = true;
		//user = User.GetById(1);
	}

	public void OnGUI()
	{
		
		/*if(!isGameReady && user.IsReady())
		{
			PhotonNetwork.playerName = user.Username;
			isGameReady = true;
		} */

		if(isGameReady){

			GUI.skin.font = font;

			if(currentMenu == Menu.SelectionMenu)
				this.SelectionMenu();
		
			else if(currentMenu == Menu.CreateRoom)
				this.CreateRoomMenu();
		
			else if(currentMenu == Menu.RoomList)
				this.JoinRoomMenu();
		}
	}

	#endregion

	public void SetId(string userId){

		user = User.GetById(int.Parse(userId));

	}



	public void PhotonNetworkConnectionCheck()
	{
		GUI.skin.font = arialFont;
		if (!PhotonNetwork.connected)
		{	
			if (PhotonNetwork.connectionState == ConnectionState.Connecting)
			{
				GUILayout.Label("Connecting " + PhotonNetwork.ServerAddress);
				GUILayout.Label(Time.time.ToString());
			}
			else
			{
				GUILayout.Label("Not connected. Check console output.");
			}
			
			if (this.connectFailed)
			{
				GUILayout.Label("Connection failed. Contact Web Administrator.");
				GUILayout.Label(String.Format("Server: {0}:{1}", new object[] {PhotonNetwork.ServerAddress, PhotonNetwork.PhotonServerSettings.ServerPort}));
				GUILayout.Label("AppId: " + PhotonNetwork.PhotonServerSettings.AppID);
				
				if (GUILayout.Button("Try Again", GUILayout.Width(100)))
				{
					this.connectFailed = false;
					PhotonNetwork.ConnectUsingSettings("1.0");
				}
			}
			
		}

	}

	public void InitializeCommonStuffs()
	{
		background = new GUIStyle("Box");
		background.normal.background = textureBackground;
		dashboardBox = new GUIStyle("Box");
		dashboardBox.normal.background = textureDashboardBox;
		button = new GUIStyle("box");
		button.normal.background = textureButton;
		button.alignment = TextAnchor.MiddleCenter;
		button.hover.background = textureHoveredButton;
		button.hover.textColor = Color.white;
		button.active.background = textureClickedButton;
		button.active.textColor = Color.white;
		guiLayoutOptions[0] = GUILayout.Width(200);
		guiLayoutOptions[1] = GUILayout.Height(50);
		textbox = new GUIStyle("box");
		textbox.normal.background = textureTextbox;
		textbox.alignment = TextAnchor.MiddleLeft;
		textbox.normal.textColor = Color.black;
		guiLayoutOptionsTextbox[0] = GUILayout.Width(340);
		guiLayoutOptionsTextbox[1] = GUILayout.Height(30);

	}

	public void CreateRoomMenu()
	{
		this.InitializeCommonStuffs();

		plusButton = new GUIStyle("Box");
		plusButton.normal.background = textureButton;
		plusButton.fontSize = 20;
		plusButton.hover.background = textureHoveredButton;
		plusButton.hover.textColor = Color.white;
		plusButton.active.background = textureClickedButton;
		plusButton.active.textColor = Color.white;
		plusButton.alignment = TextAnchor.MiddleCenter;



		plusMinus = new GUILayoutOption[2];
		plusMinus[0] = GUILayout.Width(30);
		plusMinus[1] = GUILayout.Height(30);

		numberOfPlayersGui = new GUIStyle();
		numberOfPlayersGui.normal.textColor = Color.white;
		numberOfPlayersGui.margin.top = 5;
		numberOfPlayersGui.fontSize = 25;

		GUI.Box(new Rect((Screen.width - 960) / 2, (Screen.height - 600) / 2, 960, 600), "",background);
		GUI.Box(new Rect((Screen.width - 500) / 2, (Screen.height - 400) / 2, 500, 400), "",dashboardBox);
		GUILayout.BeginArea(new Rect((Screen.width - 400) / 2, (Screen.height - 300) / 2, 400, 300));
		GUILayout.BeginHorizontal();

			GUILayout.Label("Room name:", GUILayout.Width(130));
			this.roomName = GUILayout.TextField(this.roomName, 100, textbox, guiLayoutOptionsTextbox);
			
		GUILayout.EndHorizontal();

		GUILayout.Space(15);

		GUILayout.BeginHorizontal();
		
		GUILayout.Label("# of players: ",  GUILayout.Width(130));

		GUILayout.Label( " " + numberOfPlayers[currentNumberOfPlayers].ToString(),numberOfPlayersGui, GUILayout.Width(100));
		

		if(GUILayout.Button("+", plusButton, plusMinus) && currentNumberOfPlayers < numberOfPlayers.Length - 1)
		{
			rpsAudio.PlayAudio(ButtonToPlay.Accept);
			currentNumberOfPlayers++;
			
		}

		else if(GUILayout.Button("-", plusButton, plusMinus) && currentNumberOfPlayers > 0)
		{
			rpsAudio.PlayAudio(ButtonToPlay.Accept);
			currentNumberOfPlayers--;
			
		} 
		

		GUILayout.EndHorizontal();
		GUILayout.Space(15);
		GUILayout.BeginHorizontal();

		guiLayoutOptions[0] = GUILayout.Width(130);
		guiLayoutOptions[1] = GUILayout.Height(40);
		button.fontSize = 18;
		if (GUILayout.Button("Create", button,guiLayoutOptions))
		{
			rpsAudio.PlayAudio(ButtonToPlay.Accept);
			string temp = roomName.Replace(" ",string.Empty);

			if(temp.Length != 0)
			{
				PhotonNetwork.playerName = playerName;
				PhotonNetwork.CreateRoom(this.roomName, true, true, numberOfPlayers[currentNumberOfPlayers]);
			}
			else
				roomNameError = "Room name is required.";
		}
		GUILayout.EndHorizontal();
		GUILayout.Space(10);
		GUILayout.BeginHorizontal();
		if (GUILayout.Button("Back", button,guiLayoutOptions))
		{
			rpsAudio.PlayAudio(ButtonToPlay.Back);
			currentMenu = Menu.SelectionMenu;
			
		}
		GUILayout.EndHorizontal();
		GUILayout.Space(20);
		GUILayout.BeginHorizontal();
		GUILayout.Label(roomNameError,GUILayout.Width(300));
		GUILayout.EndHorizontal();
		
		PhotonNetworkConnectionCheck();
		GUILayout.EndArea();
	}

	public void SelectionMenu()
	{

		this.InitializeCommonStuffs();
		GUI.Box(new Rect((Screen.width - 960) / 2, (Screen.height - 600) / 2, 960, 600), "",background);
		GUI.Box(new Rect((Screen.width - 400) / 2, (Screen.height - 400) / 2, 400, 400), "",dashboardBox);
		GUILayout.BeginArea(new Rect((Screen.width - 200) / 2, (Screen.height - 300) / 2, 200, 300));

		GUILayout.BeginHorizontal();
			GUILayout.Label("Name:", GUILayout.Width(60));
			playerName = GUILayout.TextField(playerName, 100, textbox, guiLayoutOptionsTextbox);
		GUILayout.EndHorizontal();
		GUILayout.Space(10);
			if (GUILayout.Button("Create Game", button,guiLayoutOptions))
			{
				rpsAudio.PlayAudio(ButtonToPlay.Accept);
				currentMenu = Menu.CreateRoom;
				//PhotonNetwork.CreateRoom(this.roomName, true, true, numberOfPlayers[currentNumberOfPlayers]);
				
			} 
		GUILayout.Space(15);

			if (GUILayout.Button("Join Game", button ,guiLayoutOptions))
			{
				rpsAudio.PlayAudio(ButtonToPlay.Accept);
				currentMenu = Menu.RoomList;
				//PhotonNetwork.JoinRoom(this.roomName);
			} 
		GUILayout.Space(15);
		
		if (GUILayout.Button("Single Player", button ,guiLayoutOptions))
		{
			rpsAudio.PlayAudio(ButtonToPlay.Accept);
			Application.LoadLevel(SceneNameSinglePlay);
		}
		GUILayout.Space(15);

			if (GUILayout.Button("Quit", button ,guiLayoutOptions))
			{
				rpsAudio.PlayAudio(ButtonToPlay.Back);
				if(Application.isWebPlayer)
					Application.OpenURL("http://www.rockthepaper.net");
				else
					Application.Quit();
				
			}

		Debug.Log(PhotonNetwork.countOfPlayers);
		
		PhotonNetworkConnectionCheck();
		GUILayout.EndArea(); 


	}

	public void JoinRoomMenu()
	{

		this.InitializeCommonStuffs();

		roomListLabel = new GUIStyle();
		roomListLabel.normal.textColor = Color.black;
		roomListLabel.margin.left = 10;
		roomListLabel.margin.top = 7;

		joinGameBox = new GUIStyle("Box");
		joinGameBox.normal.background = textureJoinGameBox;
		joinGameBox.normal.textColor = Color.black;

		GUI.Box(new Rect((Screen.width - 960) / 2, (Screen.height - 600) / 2, 960, 600), "",background);
		GUI.Box(new Rect((Screen.width - 600) / 2, (Screen.height - 400) / 2, 600, 400), "",dashboardBox);
		GUILayout.BeginArea(new Rect((Screen.width - 500) / 2, (Screen.height - 300) / 2, 500, 300));
		if (PhotonNetwork.GetRoomList().Length == 0)
		{
			GUILayout.Label("Currently no games are available.");
			GUILayout.Label("Rooms will be listed here when they become ");
			GUILayout.Label("available.");
		}
		else
		{
			this.scrollPos = GUILayout.BeginScrollView(this.scrollPos);
			foreach (RoomInfo roomInfo in PhotonNetwork.GetRoomList())
			{
				GUILayout.BeginHorizontal(joinGameBox);
				GUILayout.Label(roomInfo.name ,roomListLabel, GUILayout.Width(180));
				GUILayout.Label(roomInfo.playerCount + "/" + (roomInfo.maxPlayers) , roomListLabel, GUILayout.Width(180));
				if(GUILayout.Button("Join",button) && roomInfo.playerCount < (roomInfo.maxPlayers))
				{
					errorOnJoin = "";
					rpsAudio.PlayAudio(ButtonToPlay.Accept);
					PhotonNetwork.playerName = playerName;
					PhotonNetwork.JoinRoom(roomInfo.name);
					Debug.Log("I was here");
				}
				GUILayout.EndHorizontal();
			}
			GUILayout.Space(10);
			roomListLabel.normal.textColor = Color.white;
			GUILayout.Label(errorOnJoin,roomListLabel,GUILayout.Width(200));

			GUILayout.EndScrollView();
		}

		GUILayout.Space(10);

		if(GUILayout.Button("Back",button,GUILayout.Width(100), GUILayout.Height(30)))
		{
			rpsAudio.PlayAudio(ButtonToPlay.Back);
			currentMenu = Menu.SelectionMenu;

		}
		PhotonNetworkConnectionCheck();
		GUILayout.EndArea();



	}

	#region photon methods

	public void OnJoinedRoom()
	{
		Debug.Log("OnJoinedRoom");
	}
	
	public void OnCreatedRoom()
	{
		Debug.Log("OnCreatedRoom");
		//PhotonNetwork.room.maxPlayers = numberOfPlayers[currentNumberOfPlayers];
		PhotonNetwork.LoadLevel(SceneNameGame);
	}
	
	public void OnPhotonJoinRoomFailed()
	{

		errorOnJoin = "Room is full or joining is failed. Please Retry.";

	}

	public void OnDisconnectedFromPhoton()
	{
		Debug.Log("Disconnected from Photon.");
	}
	
	public void OnFailedToConnectToPhoton(object parameters)
	{
		this.connectFailed = true;
		Debug.Log("OnFailedToConnectToPhoton. StatusCode: " + parameters);
	}

	#endregion

	public enum Menu { CreateRoom, SelectionMenu, RoomList }
}
