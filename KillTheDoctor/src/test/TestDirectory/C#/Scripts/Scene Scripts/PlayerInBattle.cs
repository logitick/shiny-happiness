using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using Game;


public class PlayerInBattle : Photon.MonoBehaviour 
{

	public Transform playerPrefab;

	private PlayerList playerList;
	private Player playerOne;
	private Player playerTwo;

	void Awake()
	{
		PhotonNetwork.Instantiate(this.playerPrefab.name, transform.position, Quaternion.identity, 0);
		GameObject playerInfoObject = GameObject.Find("PlayerList");
		playerList = playerInfoObject.GetComponent<PlayerList>();

		this.AssignPlayers(); 
	
	}

	void Start () 
	{


	}

	void Update () 
	{
		
	}

	public void AssignPlayers()
	{


		for(int i = 0; i < playerList.players.Count; i += 2)
		{

			playerOne = playerList.players[i];
			playerTwo = playerList.players[i + 1];
			playerOne.PhotonPlayerEnemy = playerTwo.PhotonPlayer;
			playerTwo.PhotonPlayerEnemy = playerOne.PhotonPlayer;


		}

	}

	public void ReArrangePlayers()
	{


	}
}
