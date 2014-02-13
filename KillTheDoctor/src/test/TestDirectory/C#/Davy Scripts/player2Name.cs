using UnityEngine;
using System.Collections;

public class player2Name : MonoBehaviour {

	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		changeName();
	}

	void changeName(){
		string p2Name = GameObject.Find("Players Object").transform.GetChild(1).transform.GetComponent<player2Controller>().player2Name;
		guiText.text = p2Name;
	}
}
