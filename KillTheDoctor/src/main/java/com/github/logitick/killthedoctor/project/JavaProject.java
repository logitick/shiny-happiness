package com.github.logitick.killthedoctor.project;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public class JavaProject extends ProjectType {
  @Override
  public String getName() {
    return "JavaProject";
  }

  @Override
  public void setProjectExtensions() {
    addExtension(".java");
    addExtension(".pom");
  }
}
