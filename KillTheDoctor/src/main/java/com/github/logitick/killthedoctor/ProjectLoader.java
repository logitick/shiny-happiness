package com.github.logitick.killthedoctor;

import java.io.File;
import java.nio.file.Path;
import java.nio.file.Paths;

/**
 * Created by Knif3r on 1/25/14.
 */
public class ProjectLoader {
  private Path projectPath;
  private ProjectType type;

  public static final int ALL = 000;
  public static final int PROJECT_FILES = 001;

  private ProjectLoader(Path path, ProjectType type) {
    this.setPath(path);
    this.setType(type);
  }

  private ProjectLoader() {

  }

  public void setPath(String path) {
    this.projectPath = Paths.get(path);
  }
  public void setPath(Path path) {
    this.projectPath = path;
  }

  public Path getPath() {
    return this.projectPath;
  }

  public ProjectType getType() {
    return type;
  }

  public void setType(ProjectType type) {
    this.type = type;
  }

  public static ProjectLoader load(String path) {
    ProjectLoader loader = new ProjectLoader();
    loader.setPath(path);
    return loader;
  }

  public static ProjectLoader load(Path path, ProjectType type) {
    return new ProjectLoader(path, type);
  }

  public File[] getFiles() {
    return getFiles(ProjectLoader.ALL);
  }



  public File[] getFiles(int fetchOptions) {
    File[] files = null;

    switch (fetchOptions) {
      case ProjectLoader.ALL:
        files = this.projectPath.toFile().listFiles();
        break;
      case ProjectLoader.PROJECT_FILES:

        files = this.projectPath.toFile().listFiles(type.getFileFilter());

        break;
    }
    return files;
  }
}
