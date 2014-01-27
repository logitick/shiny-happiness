using UnityEngine;
using System.Collections;

public class PlayerInGame : Photon.MonoBehaviour
{
	public Transform playerPrefab;

	void Awake()
	{
		//PhotonNetwork.automaticallySyncScene = true;
		PhotonNetwork.Instantiate(this.playerPrefab.name, transform.position, Quaternion.identity, 0);

	}

	void Start () 
	{
	
	}

	void Update () 
	{
	
	}
		
	public void OnMasterClientSwitched(PhotonPlayer player)
	{
		Debug.Log("OnMasterClientSwitched: " + player);
		/*
		string message;
		InRoomChat chatComponent = GetComponent<InRoomChat>();  // if we find a InRoomChat component, we print out a short message

		if (chatComponent != null)
		{
			// to check if this client is the new master...
			if (player.isLocal)
			{
				message = "You are Master Client now.";
			}
			else
			{
				message = player.name + " is Master Client now.";
			}
			
			
			chatComponent.AddLine(message); // the Chat method is a RPC. as we don't want to send an RPC and neither create a PhotonMessageInfo, lets call AddLine()
		} */
	}
	

	public void OnPhotonInstantiate(PhotonMessageInfo info)
	{
		Debug.Log("OnPhotonInstantiate " + info.sender);    // you could use this info to store this or react
	}
	
	public void OnPhotonPlayerConnected(PhotonPlayer player)
	{
		Debug.Log("OnPhotonPlayerConnected: " + player);
	}
	

	
	public void OnFailedToConnectToPhoton()
	{
		Debug.Log("OnFailedToConnectToPhoton");
		
		// back to main menu        
		Application.LoadLevel(DashboardMenu.SceneNameMenu);
	}
}
