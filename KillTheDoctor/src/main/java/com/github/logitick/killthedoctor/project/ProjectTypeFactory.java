package com.github.logitick.killthedoctor.project;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public class ProjectTypeFactory {
  public static final int PHP = 001;
  public static final int JAVA = 002;
  public static final int C_SHARP = 004;

  public ProjectType createProjectType(int type) {

    ProjectType typeInstance;
    switch (type) {
      case ProjectTypeFactory.PHP: typeInstance = new PhpProject(); break;
      case ProjectTypeFactory.JAVA: typeInstance = new JavaProject(); break;
      case ProjectTypeFactory.C_SHARP: typeInstance = new CSharpProject(); break;
      default: throw new IllegalArgumentException("Unknown project type");
    }
    return typeInstance;
  }



}
