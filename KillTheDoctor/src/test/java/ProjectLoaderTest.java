/**
 * Created by Knif3r on 1/25/14.
 */
import com.github.logitick.killthedoctor.ProjectLoader;
import com.github.logitick.killthedoctor.ProjectType;
import org.junit.*;
import org.junit.Test;
import java.io.File;

import java.nio.file.Paths;


public class ProjectLoaderTest {

  private File rootDir;
  private final String strPath = "./KillTheDoctor/src/test/TestDirectory";


  @Before
  public void setUp() {
    rootDir = new File("./KillTheDoctor/src/test/TestDirectory");

  }

  @Test
  public void testCSProjectType() {
    ProjectLoader csProject = ProjectLoader.load(Paths.get(strPath), new ProjectType(ProjectType.C_SHARP));

    File[] files = csProject.getFiles(ProjectLoader.PROJECT_FILES);
    for (File file : files) {
      System.out.println(file.getName());
      for (File subFile : file.listFiles()) {
        System.out.println(subFile);
      }
    }
    System.out.println(csProject.getPath().toFile().getAbsolutePath());
    Assert.assertNotNull("project has sub files", files);
    Assert.assertTrue("Project has more than zero files", files.length > 0);
  }
}
