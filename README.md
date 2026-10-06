# Terraform + LocalStack + Docker + WordPress

Ambiente local para demonstrar Infrastructure as Code utilizando Terraform, LocalStack, Docker, WordPress, MySQL e S3.

## Pré-requisitos

- Linux
- Docker
- Docker Compose
- Terraform
- Git
- Token do LocalStack, se exigido pela versão da imagem utilizada

# Tutorial de instalação para distros Linux com base ubuntu

## 1. Atualizar o sistema

```bash
sudo apt update
sudo apt upgrade -y
```

## 2. Instalar Git

```bash
sudo apt install -y git
git --version
```

## 3. Instalar Docker

```bash
sudo apt install -y docker.io docker-compose-plugin
```

```bash
sudo systemctl enable --now docker
```

Verificar:

```bash
docker --version
docker compose version
sudo systemctl status docker
```

Opcionalmente, permitir executar Docker sem `sudo`:

```bash
sudo usermod -aG docker $USER
```

Depois, encerre a sessão e entre novamente.

Teste:

```bash
docker run hello-world
```

## 4. Instalar Terraform

Adicionar o repositório oficial da HashiCorp:

```bash
sudo apt-get update
sudo apt-get install -y gnupg software-properties-common
```

```bash
wget -O- https://apt.releases.hashicorp.com/gpg | gpg --dearmor | sudo tee /usr/share/keyrings/hashicorp-archive-keyring.gpg > /dev/null
```

```bash
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
```

```bash
sudo apt update
sudo apt install -y terraform
```

Verificar:

```bash
terraform version
```

## 5. Obter o projeto

Caso o projeto esteja em um repositório Git:

```bash
git clone <URL_DO_REPOSITORIO>
cd terraform-wordpress-local
```

Caso o projeto já esteja disponível localmente:

```bash
cd terraform-wordpress-local
```

## 6. Configurar o LocalStack

Criar o arquivo `.env` na raiz:

```bash
nano .env
```

Adicionar:

```env
LOCALSTACK_AUTH_TOKEN=COLE_SEU_TOKEN_AQUI
```

Não compartilhe o token e não o envie para o Git.

Verificar:

```bash
cat .gitignore
```

O `.gitignore` deve conter:

```text
.env
```

## 7. Iniciar o ambiente Docker

Na raiz do projeto:

```bash
sudo docker compose up -d
```

Verificar:

```bash
sudo docker compose ps
```

Logs, se necessário:

```bash
sudo docker compose logs
```

Logs individuais:

```bash
sudo docker logs localstack
sudo docker logs wordpress
sudo docker logs wordpress-mysql
```

## 8. Inicializar o Terraform

```bash
cd terraform
terraform init
```

Validar:

```bash
terraform validate
```

## 9. Visualizar o plano

```bash
terraform plan
```

## 10. Criar a infraestrutura

```bash
terraform apply
```

Quando solicitado:

```text
yes
```

## 11. Verificar o Terraform

```bash
terraform state list
```

```bash
terraform output
```

## 12. Verificar o bucket S3

Voltar para a raiz:

```bash
cd ..
```

Listar buckets:

```bash
sudo docker exec localstack awslocal s3 ls
```

Listar objetos:

```bash
sudo docker exec localstack awslocal s3 ls s3://wordpress-local-assets --recursive
```

O objeto inicial criado pelo Terraform deve aparecer:

```text
README.txt
```

## 13. Acessar o WordPress

Abrir:

```text
http://localhost:8080
```

Concluir a configuração inicial caso seja a primeira execução.

## 14. Ativar o plugin S3

No WordPress:

```text
Plugins
→ Plugins instalados
→ LocalStack S3 Integration
→ Ativar
```

## 15. Testar o upload para o S3

No WordPress:

```text
Mídia
→ Adicionar nova mídia
```

Enviar uma imagem.

Depois:

```bash
sudo docker exec localstack awslocal s3 ls s3://wordpress-local-assets --recursive
```

O arquivo enviado deve aparecer em um caminho semelhante a:

```text
uploads/2026/10/imagem.jpg
```

## 16. Verificar logs do WordPress

Se o arquivo não aparecer:

```bash
sudo docker logs wordpress --tail 100
```

Verificar o plugin:

```bash
sudo docker exec wordpress ls /var/www/html/wp-content/plugins/localstack-s3
```

Verificar a biblioteca AWS SDK:

```bash
sudo docker exec wordpress ls /var/www/html/vendor
```

## 17. Verificar o LocalStack

```bash
sudo docker logs localstack --tail 100
```

```bash
sudo docker exec localstack awslocal s3 ls
```

## 18. Parar o ambiente

```bash
sudo docker compose down
```

Esse comando para e remove os containers, mas mantém os volumes.

## 19. Iniciar novamente

```bash
sudo docker compose up -d
```

Verificar:

```bash
sudo docker compose ps
```

## 20. Recriar os recursos Terraform

Para remover os recursos gerenciados pelo Terraform:

```bash
cd terraform
terraform destroy
```

Confirmar:

```text
yes
```

Para recriar:

```bash
terraform apply
```

## 21. Remover containers e volumes

**Atenção:** remove também os dados persistentes dos volumes.

```bash
sudo docker compose down -v
```

Depois:

```bash
sudo docker compose up -d
```

E recriar os recursos:

```bash
cd terraform
terraform apply
```

## Fluxo resumido

```bash
cd terraform-wordpress-local

sudo docker compose up -d

cd terraform
terraform init
terraform validate
terraform plan
terraform apply

cd ..

sudo docker exec localstack awslocal s3 ls
sudo docker exec localstack awslocal s3 ls s3://wordpress-local-assets --recursive
```

Depois acessar:

```text
http://localhost:8080
```

Enviar uma imagem pelo WordPress e verificar:

```bash
sudo docker exec localstack awslocal s3 ls s3://wordpress-local-assets --recursive
```

## Arquitetura

```text
                    Terraform
                        |
                        v
                 +-------------+
                 |  LocalStack |
                 |     S3      |
                 +------+------+
                        ^
                        |
                     upload
                        |
                 +------+------+
                 |  WordPress  |
                 |   Plugin    |
                 +------+------+
                        |
                        v
                     MySQL

        Serviços de execução dentro do Docker
```

## Tecnologias

- Terraform
- HashiCorp AWS Provider
- LocalStack
- Docker
- Docker Compose
- WordPress
- MySQL
- Amazon S3 (simulado pelo LocalStack)
- AWS SDK for PHP

## Observação

Este projeto utiliza o LocalStack para executar localmente a integração com serviços AWS. O ambiente não representa uma infraestrutura AWS real e não requer recursos reais da AWS.
