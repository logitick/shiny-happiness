using UnityEngine;
using System.Collections;

public class Timer : MonoBehaviour {

	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		changeText();
	}
	void changeText(){
		string timer = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().timetext;
		//System.Convert.ToInt32(timer);
		if(timer.Length > 0)
		transform.guiText.text = System.Convert.ToInt32(timer).ToString();
	}
}
