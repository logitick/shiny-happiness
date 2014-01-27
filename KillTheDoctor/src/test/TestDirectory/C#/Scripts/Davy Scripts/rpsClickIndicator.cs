using UnityEngine;
using System.Collections;

public class rpsClickIndicator : MonoBehaviour {
	public bool isSelected;
	public string objName = "null";
	// Use this for initialization
	void Start () {
		isSelected = false;
		objName = this.name;
	}
	
	// Update is called once per frame
	void Update () {

	}
}
