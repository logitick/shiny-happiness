using UnityEngine;
using System.Collections;

public class player1Name : MonoBehaviour {

	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		changeName();
	}

	void changeName(){
		string p1Name = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().player1Name;
		guiText.text = p1Name;
	}
}
