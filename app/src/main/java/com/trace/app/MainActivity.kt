package com.trace.app

import android.content.Context
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.room.*
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.launch

// --- Room Database Layer ---
@Entity(tableName = "trace_records")
data class TraceEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val timestamp: Long = System.currentTimeMillis(),
    val assertion: String,
    val directOrigin: String,
    val ancestralOrigin: String,
    val beneficiary: String,
    val category: String,
    val costHours: Float,
    val costDollars: Float
)

@Dao
interface TraceDao {
    @Insert
    suspend fun insertTrace(trace: TraceEntity)

    @Query("SELECT * FROM trace_records ORDER BY timestamp DESC")
    fun getAllTraces(): Flow<List<TraceEntity>>

    @Query("DELETE FROM trace_records WHERE id = :id")
    suspend fun deleteTrace(id: Long)
}

@Database(entities = [TraceEntity::class], version = 1, exportSchema = false)
abstract class AppDatabase : RoomDatabase() {
    abstract fun traceDao(): TraceDao

    companion object {
        @Volatile
        private var INSTANCE: AppDatabase? = null

        fun getDatabase(context: Context): AppDatabase {
            return INSTANCE ?: synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    AppDatabase::class.java,
                    "trace_local_db"
                ).build()
                INSTANCE = instance
                instance
            }
        }
    }
}

// --- ViewModel ---
class TraceViewModel(private val dao: TraceDao) : ViewModel() {
    val allTraces: Flow<List<TraceEntity>> = dao.getAllTraces()

    fun saveTrace(
        assertion: String,
        directOrigin: String,
        ancestralOrigin: String,
        beneficiary: String,
        category: String,
        costHours: Float,
        costDollars: Float
    ) {
        viewModelScope.launch {
            dao.insertTrace(
                TraceEntity(
                    assertion = assertion,
                    directOrigin = directOrigin,
                    ancestralOrigin = ancestralOrigin,
                    beneficiary = beneficiary,
                    category = category,
                    costHours = costHours,
                    costDollars = costDollars
                )
            )
        }
    }

    fun deleteTrace(id: Long) {
        viewModelScope.launch {
            dao.deleteTrace(id)
        }
    }
}

class TraceViewModelFactory(private val dao: TraceDao) : ViewModelProvider.Factory {
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(TraceViewModel::class.java)) {
            @Suppress("UNCHECKED_CAST")
            return TraceViewModel(dao) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}

// --- Jetpack Compose UI ---
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        val database = AppDatabase.getDatabase(this)
        val viewModelFactory = TraceViewModelFactory(database.traceDao())

        setContent {
            MaterialTheme(colorScheme = darkColorScheme()) {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    val viewModel: TraceViewModel = viewModel(factory = viewModelFactory)
                    TraceApp(viewModel)
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TraceApp(viewModel: TraceViewModel) {
    var currentTab by remember { mutableIntStateOf(0) }
    val traces by viewModel.allTraces.collectAsState(initial = emptyList())

    Scaffold(
        topBar = { TopAppBar(title = { Text("THE TRACE PROTOCOL") }) },
        bottomBar = {
            NavigationBar {
                NavigationBarItem(
                    selected = currentTab == 0,
                    onClick = { currentTab = 0 },
                    label = { Text("New Trace") },
                    icon = {}
                )
                NavigationBarItem(
                    selected = currentTab == 1,
                    onClick = { currentTab = 1 },
                    label = { Text("Saved Vault (${traces.size})") },
                    icon = {}
                )
            }
        }
    ) { padding ->
        Box(modifier = Modifier.padding(padding)) {
            if (currentTab == 0) {
                TraceWizard(onCommit = { assertion, direct, ancestral, beneficiary, category, hours, dollars ->
                    viewModel.saveTrace(assertion, direct, ancestral, beneficiary, category, hours, dollars)
                    currentTab = 1
                })
            } else {
                VaultList(traces = traces, onDelete = { viewModel.deleteTrace(it) })
            }
        }
    }
}

@Composable
fun TraceWizard(onCommit: (String, String, String, String, String, Float, Float) -> Unit) {
    var step by remember { mutableIntStateOf(1) }
    var assertion by remember { mutableStateOf("") }
    var directOrigin by remember { mutableStateOf("") }
    var ancestralOrigin by remember { mutableStateOf("") }
    var beneficiary by remember { mutableStateOf("") }
    var category by remember { mutableStateOf("Financial") }
    var hours by remember { mutableStateOf("") }
    var dollars by remember { mutableStateOf("") }

    Column(
        modifier = Modifier.fillMaxSize().padding(24.dp),
        verticalArrangement = Arrangement.SpaceBetween
    ) {
        Column {
            LinearProgressIndicator(
                progress = { step / 4f },
                modifier = Modifier.fillMaxWidth()
            )
            Spacer(modifier = Modifier.height(24.dp))

            when (step) {
                1 -> {
                    Text("Stage 1: The Assertion", style = MaterialTheme.typography.titleLarge)
                    Text("Identify the core belief or rule that makes your life heavy.", style = MaterialTheme.typography.bodyMedium)
                    Spacer(modifier = Modifier.height(12.dp))
                    OutlinedTextField(
                        value = assertion,
                        onValueChange = { assertion = it },
                        label = { Text("Core Belief / Assertion") },
                        modifier = Modifier.fillMaxWidth().height(140.dp)
                    )
                }
                2 -> {
                    Text("Stage 2: The Genealogy", style = MaterialTheme.typography.titleLarge)
                    Text("Trace the transmission lineage of this rule.", style = MaterialTheme.typography.bodyMedium)
                    Spacer(modifier = Modifier.height(12.dp))
                    OutlinedTextField(
                        value = directOrigin,
                        onValueChange = { directOrigin = it },
                        label = { Text("Who taught you this?") },
                        modifier = Modifier.fillMaxWidth()
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedTextField(
                        value = ancestralOrigin,
                        onValueChange = { ancestralOrigin = it },
                        label = { Text("Who taught them?") },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
                3 -> {
                    Text("Stage 3: The Beneficiary", style = MaterialTheme.typography.titleLarge)
                    Text("Who profits from your continued belief in this rule?", style = MaterialTheme.typography.bodyMedium)
                    Spacer(modifier = Modifier.height(12.dp))
                    OutlinedTextField(
                        value = beneficiary,
                        onValueChange = { beneficiary = it },
                        label = { Text("Primary Beneficiary") },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
                4 -> {
                    Text("Stage 4: The Cost", style = MaterialTheme.typography.titleLarge)
                    Text("Quantify the monthly drain required to maintain this rule.", style = MaterialTheme.typography.bodyMedium)
                    Spacer(modifier = Modifier.height(12.dp))
                    OutlinedTextField(
                        value = hours,
                        onValueChange = { hours = it },
                        label = { Text("Monthly Hours Drained") },
                        modifier = Modifier.fillMaxWidth()
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedTextField(
                        value = dollars,
                        onValueChange = { dollars = it },
                        label = { Text("Monthly Dollars Spent") },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            }
        }

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            if (step > 1) {
                OutlinedButton(onClick = { step-- }) { Text("Back") }
            } else {
                Spacer(modifier = Modifier.width(1.dp))
            }

            if (step < 4) {
                Button(
                    onClick = { step++ },
                    enabled = assertion.isNotBlank()
                ) { Text("Next") }
            } else {
                Button(
                    onClick = {
                        onCommit(
                            assertion, directOrigin, ancestralOrigin, beneficiary, category,
                            hours.toFloatOrNull() ?: 0f, dollars.toFloatOrNull() ?: 0f
                        )
                    }
                ) { Text("Commit Trace") }
            }
        }
    }
}

@Composable
fun VaultList(traces: List<TraceEntity>, onDelete: (Long) -> Unit) {
    if (traces.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("No saved traces in local vault.")
        }
    } else {
        LazyColumn(
            modifier = Modifier.fillMaxSize().padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            items(traces) { trace ->
                Card(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text(text = trace.assertion, style = MaterialTheme.typography.titleMedium)
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(text = "Source: ${trace.directOrigin} → ${trace.ancestralOrigin}", style = MaterialTheme.typography.bodySmall)
                        Text(text = "Beneficiary: ${trace.beneficiary}", style = MaterialTheme.typography.bodySmall)
                        Text(text = "Cost: ${trace.costHours} hrs/mo | \$${trace.costDollars}/mo", style = MaterialTheme.typography.bodySmall)
                        Spacer(modifier = Modifier.height(8.dp))
                        TextButton(onClick = { onDelete(trace.id) }) {
                            Text("Purge Record", color = MaterialTheme.colorScheme.error)
                        }
                    }
                }
            }
        }
    }
}
