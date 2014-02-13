package com.github.logitick.killthedoctor.project;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public class PhpProject extends ProjectType {
  @Override
  public String getName() {
    return "PHP";
  }

  @Override
  public void setProjectExtensions() {
    this.addExtension("php");
    this.addExtension("inc");
    this.addExtension("htaccess");
  }
}
