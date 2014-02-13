package com.github.logitick.killthedoctor;

import com.github.logitick.killthedoctor.project.ProjectType;

import java.io.File;
import java.nio.file.Path;
import java.nio.file.Paths;

/**
 * Created by Paul Daniel Iway on 1/25/14.
 */
public class Project {
  private Path projectPath;
  private ProjectType type;

  public static final int ALL = 000;
  public static final int PROJECT_FILES = 001;

  private Project(Path path, ProjectType type) {
    this.setPath(path);
    this.setType(type);
  }

  private Project() {

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

  public static Project
  load(String path) {
    Project loader = new Project();
    loader.setPath(path);
    return loader;
  }

  public static Project load(Path path, ProjectType type) {
    return new Project(path, type);
  }

  public File[] getFiles() {
    return getFiles(Project.ALL);
  }



  public File[] getFiles(int fetchOptions) {
    return getFiles(this.projectPath.toFile(), fetchOptions);
  }

  private File[] getFiles(File current, int fetchOptions) {
    File[] files = null;
    switch (fetchOptions) {
      case Project.PROJECT_FILES:
        files = current.listFiles(type.getFileFilter());
        break;
      default:
        files = current.listFiles();
    }
    for (File file : files) {
      if (file.isDirectory()) {
        System.arraycopy(this.getFiles(file, fetchOptions), 0, files, 0, files.length);
      }
    }

    return files;
  }
}
