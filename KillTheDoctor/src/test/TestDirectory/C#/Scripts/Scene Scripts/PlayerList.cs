using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using Game;

[System.Serializable]
public class PlayerList : MonoBehaviour 
{

	public List<Player> players = new List<Player>();
	public List<string> messages = new List<string>();

	void Awake(){

		DontDestroyOnLoad(transform.gameObject);

	}

	void Start () 
	{
	
	}

	void Update () 
	{
	
	}

	public int GetPlayerAtIndex(string playerName)
	{

		int index = 0;
		foreach(Player p in players)
		{
			
			if(p.PlayerName == playerName)
			{
				
				return index;
				
			}
			index++;
		}
		
		return -1;

	}

	public bool IsUltimateWinner(string playerName)
	{

		foreach(Player p in players)
		{

			if(p.PlayerName != playerName && p.IsDefeated == false)
			{

				return false;

			}
	
		}

		return true;
	}

	public Player GetPlayer(string playerName)
	{

		int index = 0;
		foreach(Player p in players)
		{
			
			if(p.PlayerName == playerName)
			{
				
				return p;

				
			}
			index++;
		}

		return null;

	}

	public Player WaitingPlayer(string playerName)
	{

		foreach(Player p in players)
		{
			Debug.Log(p.PlayerName + " Is Waiting:" + p.IsWaiting);
			if(p.PlayerName != playerName && p.IsWaiting)
			{
				Debug.Log(p.PlayerName + " is waiting");
				return p;
			}
		}

		return null;
	}
}
