package com.github.logitick.killthedoctor.project;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public class CSharpProject extends ProjectType {
  @Override
  public String getName() {

    return "C Sharp";
  }

  @Override
  public void setProjectExtensions() {
    this.addExtension(".cs");
  }
}
