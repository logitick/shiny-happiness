import com.github.logitick.killthedoctor.project.CSharpProject;
import com.github.logitick.killthedoctor.project.JavaProject;
import com.github.logitick.killthedoctor.project.PhpProject;
import com.github.logitick.killthedoctor.project.ProjectType;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;

import java.io.File;
import java.io.FileFilter;

import static org.mockito.Mockito.mock;
import static org.mockito.Mockito.when;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public class ProjectTypesTest {
  File rootDir;
  File phpDir;
  File javaDir;
  File csDir;
  private File phpFile;
  private File javaFile;
  private File csFile;
  private File xmlFile;
  private File markdownFile;
  private File txtFile;
  private File sqlFile;

  @Before
  public void setUp() {
    rootDir = new File("./KillTheDoctor/src/test/TestDirectory");
    phpDir  = new File("./KillTheDoctor/src/test/TestDirectory/PHP");
    javaDir = new File("./KillTheDoctor/src/test/TestDirectory/Java");
    csDir   = new File("./KillTheDoctor/src/test/TestDirectory/C#");

    phpFile = mock(File.class);
    javaFile = mock(File.class);
    csFile = mock(File.class);
    xmlFile = mock(File.class);
    markdownFile = mock(File.class);
    txtFile = mock(File.class);
    sqlFile = mock(File.class);

    when(phpFile.getName()).thenReturn("index.php");
    when(javaFile.getName()).thenReturn("Main.java");
    when(csFile.getName()).thenReturn("Program.cs");
    when(xmlFile.getName()).thenReturn("config.xml");
    when(markdownFile.getName()).thenReturn("readme.md");
    when(txtFile.getName()).thenReturn("readme.txt");
    when(sqlFile.getName()).thenReturn("db.sql");

    // when isDirectory is invoked
    when(phpFile.isDirectory()).thenReturn(false);
    when(javaFile.isDirectory()).thenReturn(false);
    when(csFile.isDirectory()).thenReturn(false);
    when(xmlFile.isDirectory()).thenReturn(false);
    when(markdownFile.isDirectory()).thenReturn(false);
    when(txtFile.isDirectory()).thenReturn(false);
    when(sqlFile.isDirectory()).thenReturn(false);
  }

  @Test
  public void TestIsValidExtension() {
    ProjectType type = new PhpProject();
    Assert.assertTrue("txt file", type.isValidExtension("readme.txt"));
    Assert.assertTrue("sql file", type.isValidExtension("db.sql"));
    Assert.assertTrue("xml file", type.isValidExtension("config.xml"));
    Assert.assertFalse("java file", type.isValidExtension("Main.java"));
    Assert.assertFalse("c# file", type.isValidExtension("Program.cs"));
    Assert.assertFalse("md file", type.isValidExtension("readme.md"));
  }

  @Test
  public void TestIfFilterIsNotNull() {
    ProjectType type = new PhpProject();
    Assert.assertNotNull(type.getFileFilter());
    type = new JavaProject();
    Assert.assertNotNull(type.getFileFilter());
    type = new CSharpProject();
    Assert.assertNotNull(type.getFileFilter());
  }

  @Test
  public void TestFileFilterLogic() {
    ProjectType type = new PhpProject();
    FileFilter filter = type.getFileFilter();
    Assert.assertTrue("accept php files", filter.accept(phpFile));
    Assert.assertTrue("accept xml files", filter.accept(xmlFile));
    Assert.assertTrue("accept sql files", filter.accept(sqlFile));
    Assert.assertFalse("reject md files", filter.accept(markdownFile));
    Assert.assertFalse("reject java files", filter.accept(javaFile));
    Assert.assertFalse("reject c# files", filter.accept(csFile));

  }

  @Test
  public void TestPhpProjectFilter() {
    ProjectType type = new PhpProject();
    File[] files = phpDir.listFiles(type.getFileFilter());
    Assert.assertTrue("files found: " + files.length, files.length > 0);
    for(File file : files) {
      if (!file.isDirectory()) {
        Assert.assertTrue("filename: " + file.getName(), file.getName().endsWith(".php"));
      }
    }
  }

  @Test
  public void TestPhpInDifferentDirectoryProjectFilter() {
    ProjectType type = new PhpProject();
    File[] files = javaDir.listFiles(type.getFileFilter());

    for(File file : files) {
      if (!file.isDirectory()) {
        System.out.println(file.getName());
        Assert.assertTrue(file.getName().endsWith(".php"));
      }
    }
  }

}
